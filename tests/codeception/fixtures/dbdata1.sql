SET @OLD_UNIQUE_CHECKS = @@UNIQUE_CHECKS, UNIQUE_CHECKS = 0;
SET @OLD_FOREIGN_KEY_CHECKS = @@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS = 0;
SET @OLD_SQL_MODE = @@SQL_MODE, SQL_MODE = 'TRADITIONAL,ALLOW_INVALID_DATES';


INSERT INTO `consultation` (`id`, `siteId`, `urlPath`, `type`, `title`, `titleShort`, `eventDateFrom`, `eventDateTo`, `deadlineMotions`, `deadlineAmendments`, `policyMotions`, `policyAmendments`, `policyComments`, `policySupport`, `adminEmail`, `settings`) VALUES
  (1, 1, 'std-parteitag', 0, 'Test2', '', NULL, NULL, NULL, NULL, 'all', 'all', 'all', 'all', 'tobias@hoessl.eu', 0x7b226d6f74696f6e4e65656473456d61696c223a66616c73652c226d6f74696f6e4e6565647350686f6e65223a66616c73652c226d6f74696f6e48617350686f6e65223a66616c73652c22636f6d6d656e744e65656473456d61696c223a66616c73652c22696e6961746f72734d617945646974223a66616c73652c2261646d696e734d617945646974223a747275652c226d61696e7461696e616e63654d6f6465223a66616c73652c22636f6e6669726d456d61696c73223a66616c73652c226c696e654e756d626572696e67476c6f62616c223a66616c73652c22616d656e644e756d626572696e67476c6f62616c223a66616c73652c22616d656e644e756d626572696e6742794c696e65223a66616c73652c22686964655265766973696f6e223a66616c73652c226d696e696d616c69737469635549223a66616c73652c2273686f774665656473223a747275652c22636f6d6d656e7473537570706f727461626c65223a66616c73652c2273637265656e696e674d6f74696f6e73223a66616c73652c2273637265656e696e674d6f74696f6e7353686f776e223a66616c73652c2273637265656e696e67416d656e646d656e7473223a66616c73652c2273637265656e696e67436f6d6d656e7473223a66616c73652c22696e69746961746f72734d617952656a656374223a66616c73652c227469746c654861734c696e654e756d626572223a747275652c22686173504446223a747275652c226c696e654c656e677468223a38302c2273746172744c61796f757454797065223a302c226c6162656c427574746f6e4e6577223a22222c22636f6d6d656e7457686f6c654d6f74696f6e73223a66616c73652c22616c6c6f774d756c7469706c6554616773223a66616c73652c226c6f676f55726c223a6e756c6c2c226c6f676f55726c4642223a6e756c6c2c226d6f74696f6e496e74726f223a6e756c6c7d);

INSERT INTO `consultationSettingsMotionSection` (`id`, `consultationId`, `motionTypeId`, `type`, `position`, `title`, `fixedWidth`, `maxLen`, `lineNumbers`, `hasComments`) VALUES
  (1, 1, NULL, 1, 1, 'Antragstext', 1, 0, 1, 0),
  (2, 1, NULL, 1, 2, 'Begründung', 0, 0, 0, 0);

INSERT INTO `consultationSettingsMotionType` (`id`, `consultationId`, `title`, `motionPrefix`, `position`) VALUES
  (1, 1, 'Antrag', 'A', 0),
  (2, 1, 'Resolution', 'R', 1),
  (3, 1, 'Satzungsantrag', 'S', 2);

INSERT INTO `site` (`id`, `subdomain`, `title`, `titleShort`, `settings`, `currentConsultationId`, `public`, `contact`) VALUES
  (1, 'stdparteitag', 'Test2', 'Test2', 0x7b226f6e6c794e616d657370616365644163636f756e7473223a66616c73652c226f6e6c795775727a656c7765726b223a66616c73652c2277696c6c696e67546f506179223a2232227d, 1, 1, 'Test2');

INSERT INTO `siteAdmin` (`siteId`, `userId`) VALUES
  (1, 1);

INSERT INTO `user` (`id`, `name`, `email`, `emailConfirmed`, `auth`, `dateCreation`, `status`, `pwdEnc`, `authKey`, `siteNamespaceId`) VALUES
  (1, 'Testadmin', 'testadmin@example.org', 1, 'email:testadmin@example.org', '2015-03-21 11:04:44', 0, 'sha256:1000:gpdjLHGKeqKXDjjjVI6JsXF5xl+cAYm1:jT6RRYV6luIdDaomW56BMf50zQi0tiFy', NULL, NULL),
  (2, 'Testuser', 'testuser@example.org', 1, 'email:testuser@example.org', '2015-03-21 11:08:14', 0, 'sha256:1000:BwEqXMsdBXDi71XpQud1yRene4zeNRTt:atF5X6vaHJ93nyDIU/gobIpehez+0KBV', NULL, NULL);


SET SQL_MODE = @OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS = @OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS = @OLD_UNIQUE_CHECKS;
