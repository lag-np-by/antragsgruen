<?php

/**
 * A helper class for Codeception (http://codeception.com/) that allows automated accessility checks
 * (WCAG 2.0, Section508) using the pa11y (http://pa11y.org/) command line tool
 * during acceptance testing.
 * It uses local binaries and can therefore be run offline.
 *
 *
 * Requirements:
 * =============
 *
 * - Codeception with WebDriver or PhpBrowser set up
 * - pa11y is installed locally (e.g. using "npm insgall -g pa11y")
 *
 *
 * Installation:
 * =============
 *
 * - Copy this file to _support/Helper/ in the codeception directory
 * - Merge the following configuration to acceptance.suite.yml:
 *
 * modules:
 *   enabled:
 *     - \Helper\AccessibilityValidator
 *   config:
 *     \Helper\AccessibilityValidator:
 *       pa11yPath: /usr/local/bin/pa11y
 *
 *
 * Usage:
 * ======
 *
 * Validate the current site against WCAG 2.0 (AAA):
 * $I->validatePa11y(\Helper\AccessibilityValidator::STANDARD_WCAG2AAA);
 *
 * Validate the current site against WCAG 2.0 (AA):
 * $I->validatePa11y(); // or:
 * $I->validatePa11y(\Helper\AccessibilityValidator::STANDARD_WCAG2A);
 *
 * Validate the current site against WCAG 2.0 (A):
 * $I->validatePa11y(\Helper\AccessibilityValidator::STANDARD_WCAG2A);
 *
 * Validate the current site against Section 508:
 * $I->validatePa11y(\Helper\AccessibilityValidator::STANDARD_SECTION508);
 *
 * Validate against WCAG 2.0 (AA), but ignore errors containing the string "Ignoreme":
 * $I->validatePa11y(\Helper\AccessibilityValidator::STANDARD_WCAG2A, ["Ignoreme"]);
 *
 *
 *
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @author Tobias Hößl <tobias@hoessl.eu>
 */

namespace Helper;

use Codeception\TestCase;

class AccessibilityValidator extends \Codeception\Module
{
    public static $SUPPORTED_STANDARDS = array(
        'WCAG2AAA',
        'WCAG2AA',
        'WCAG2A',
        'Section508',
    );
    const STANDARD_WCAG2AAA   = 'WCAG2AAA';
    const STANDARD_WCAG2AA    = 'WCAG2AA';
    const STANDARD_WCAG2A     = 'WCAG2A';
    const STANDARD_SECTION508 = 'Section508';

    /**
     * @return string
     */
    private function getPageUrl()
    {
        if ($this->hasModule('\Helper\AntragsgruenWebDriver')) {
            /** @var \Helper\AntragsgruenWebDriver $webdriver */
            $webdriver = $this->getModule('\Helper\AntragsgruenWebDriver');
            return $webdriver->webDriver->getCurrentURL();
        } elseif ($this->hasModule('WebDriver')) {
            /** @var \Codeception\Module\WebDriver $webdriver */
            $webdriver = $this->getModule('WebDriver');
            return $webdriver->webDriver->getCurrentURL();
        } else {
            /** @var \Codeception\Module\PhpBrowser $phpBrowser */
            $phpBrowser = $this->getModule('PhpBrowser');
            return trim($phpBrowser->_getUrl(), '/') . $phpBrowser->_getCurrentUri();
        }
    }

    /**
     * @param string $url
     * @param string $standard
     * @return array
     * @throws \Exception
     */
    private function validateByPa11y($url, $standard)
    {
        if (!in_array($standard, static::$SUPPORTED_STANDARDS)) {
            throw new \Exception('Unknown standard: ' . $standard);
        }

        $pa11yPath = $this->_getConfig('pa11yPath');
        if (!$pa11yPath) {
            $pa11yPath = 'pa11y';
        }
        if (!file_exists($pa11yPath)) {
            throw new \Exception('pa11y not found: ' . $pa11yPath);
        }

        exec($pa11yPath . " -s " . $standard . " -r json '" . addslashes($url) . "'", $return);
        $data = json_decode($return[0], true);
        if (!$data) {
            $msg = 'Invalid data returned from validation service: ';
            throw new \Exception($msg . $return);
        }
        return $data;
    }

    /**
     * @param string $standard
     * @param string[] $ignoreMessages
     */
    public function validatePa11y($standard = 'WCAG2AA', $ignoreMessages = [])
    {
        try {
            $url      = $this->getPageUrl();
            $messages = $this->validateByPa11y($url, $standard);
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
            return;
        }
        $failMessages = [];
        foreach ($messages as $message) {
            if ($message['type'] == 'error') {
                $string = $message['code'] . "\n" . $message['selector'] . ': ';
                $string .= $message['context'] . "\n";
                $string .= $message['message'];
                $ignoring = false;
                foreach ($ignoreMessages as $ignoreMessage) {
                    if (mb_stripos($string, $ignoreMessage) !== false) {
                        $ignoring = true;
                    }
                }
                if (!$ignoring) {
                    $failMessages[] = $string;
                }
            }
        }
        if (count($failMessages) > 0) {
            $failStr = 'Failed ' . $standard . ' check: ' . "\n";
            $failStr .= implode("\n\n", $failMessages);
            \PHPUnit_Framework_Assert::fail($failStr);
        }
    }
}
