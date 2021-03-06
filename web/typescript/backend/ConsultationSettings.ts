/// <reference path="../typings/fuelux/index.d.ts" />

declare let Sortable: any;

export class ConsultationSettings {
    constructor(private $form: JQuery) {
        this.initLogoUpload();
        this.initUrlPath();
        this.initTags();
        this.initAdminMayEdit();
        this.initSingleMotionMode();

        $('[data-toggle="tooltip"]').tooltip();
    }

    private initUrlPath() {
        $('.urlPathHolder .shower a').click(function (ev) {
            ev.preventDefault();
            $('.urlPathHolder .shower').addClass('hidden');
            $('.urlPathHolder .holder').removeClass('hidden');
        });
    }

    private initSingleMotionMode() {
        $("#singleMotionMode").change(function () {
            if ($(this).prop("checked")) {
                $("#forceMotionRow").removeClass("hidden");
            } else {
                $("#forceMotionRow").addClass("hidden");
            }
        }).change();
    }

    private initAdminMayEdit() {
        let $adminsMayEdit = $("#adminsMayEdit"),
            $iniatorsMayEdit = $("#iniatorsMayEdit").parents("label").first().parent();
        $adminsMayEdit.change(function () {
            if ($(this).prop("checked")) {
                $iniatorsMayEdit.removeClass("hidden");
            } else {
                let confirmMessage = __t("admin", "adminMayEditConfirm");
                bootbox.confirm(confirmMessage, function (result) {
                    if (result) {
                        $iniatorsMayEdit.addClass("hidden");
                        $iniatorsMayEdit.find("input").prop("checked", false);
                    } else {
                        $adminsMayEdit.prop("checked", true);
                    }
                });
            }
        });
        if (!$adminsMayEdit.prop("checked")) $iniatorsMayEdit.addClass("hidden");
    }

    private htmlEntityDecode(html: string): string {
        const el: HTMLElement = document.createElement('div');
        el.innerHTML = html;
        return el.innerText;
    }

    private initTags() {
        this.$form.submit(() => {
            let items = $("#tagsList").pillbox('items'),
                tags = [],
                $node = $('<input type="hidden" name="tags">'),
                i;
            for (i = 0; i < items.length; i++) {
                const text = this.htmlEntityDecode(items[i].text);
                if (typeof (items[i].id) == 'undefined') {
                    tags.push({"id": 0, "name": text});
                } else {
                    tags.push({"id": items[i].id, "name": text});
                }
            }
            $node.attr("value", JSON.stringify(tags));
            this.$form.append($node);
        });

        Sortable.create(document.getElementById("tagsListUl"), {draggable: '.pill'});
    }

    private initLogoUpload() {
        const $logoRow = this.$form.find('.logoRow'),
            $uploadLabel = $logoRow.find('.uploadCol label .text');
        $logoRow.on('click', '.imageChooserDd ul    a', ev => {
            ev.preventDefault();
            const src = $(ev.currentTarget).find("img").attr("src");
            $logoRow.find('input[name=consultationLogo]').val(src);
            if ($logoRow.find('.logoPreview img').length === 0) {
                $logoRow.find('.logoPreview').prepend('<img src="" alt="">');
            }
            $logoRow.find('.logoPreview img').attr('src', src).removeClass('hidden');
            $uploadLabel.text($uploadLabel.data('title'));
            $logoRow.find("input[type=file]").val('');
        });
        $logoRow.find("input[type=file]").change(() => {
            const path = $logoRow.find("input[type=file]").val().split('\\');
            const filename = path[path.length - 1];
            $logoRow.find('input[name=consultationLogo]').val('');
            $logoRow.find(".logoPreview img").addClass('hidden');
            $uploadLabel.text(filename);
        });
    }
}
