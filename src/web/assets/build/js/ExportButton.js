class ExportButton {
    constructor() {
        this.init();

        this.settings = {};
        this.fields = {};
        this.exportEntries = this.exportEntries.bind(this);
    }

    init() {
        if(!Craft.elementIndex) return;
        this.checkConfig();
        if(!$('.export-btn').hasClass('export-btn')) {
            this.createButton();
        }
        this.updateSettings();
    }

    createButton() {
        this.createButtonXlsx();
        this.createButtonPdf();
    }
    createButtonPdf() {
        const $wrapper = $('<div />');
        $wrapper.addClass('export-btn');
        const $btn = $('<button/>');
        $btn.attr('data-icon', 'download');
        $btn.text('pdf');
        $btn.addClass('btn');
        $wrapper.append($btn);

        $btn.click(() => this.exportEntries('pdf'));
        $('.toolbar .flex').append($wrapper);
    }
    createButtonXlsx() {
        const $wrapper = $('<div />');
        $wrapper.addClass('export-btn');
        const $btn = $('<button/>');
        $btn.attr('data-icon', 'download');
        $btn.text('xlsx');
        $btn.addClass('btn');
        $wrapper.append($btn);

        $btn.click(() => this.exportEntries('xlsx'));
        $('.toolbar .flex').append($wrapper);
    }

    updateSettings() {
        this.elementIndex = Craft.elementIndex;
        const criteria = this.elementIndex.settings.criteria;
        criteria.siteId = this.elementIndex.siteId;
        criteria.search = this.elementIndex.searchText;
        criteria.status = this.elementIndex.status;
        this.settings = {
            context: this.elementIndex.settings.context,
            elementType: this.elementIndex.elementType,
            criteria: this.elementIndex.settings.criteria,
            sourceKey: this.elementIndex.sourceKey,
        };
    }

    checkConfig() {
        this.updateSettings();
        const data = this.settings;
        data[window.Craft.csrfTokenName] = window.Craft.csrfTokenValue; 
        $.post('/admin/actions/entry-export/export/config', data , function(data) {

            if(data.config){
                $('.export-btn').show();
            } else {
                $('.export-btn').hide();
            }
        });
    }
    
    loadElementSettings() {
        const data = {
            elementType: Craft.elementIndex.elementType
        };
        data[window.Craft.csrfTokenName] = window.Craft.csrfTokenValue; 
        // $.ajax ({
        //     url: '/index.php?p=admin/actions/element-index-settings/get-customize-sources-modal-data',
        //     type: "POST",
        //     data: data,
        //     dataType: "json",
        //     // contentType: "application/json; charset=utf-8",
        //     // accepts: "application/json",
        //     success: function(data){
        //         this.fields = data;
        //     }
        // });
    }
    
    exportEntries(format = 'xlsx') {

        this.updateSettings();
        // this.loadElementSettings();
        
        
        const data = this.settings;
        data[window.Craft.csrfTokenName] = window.Craft.csrfTokenValue; 
        data.format = format;
        
        // $.post('/admin/actions/entry-export/export', data);
        // return;
        

        const $form = $('<form/>');
        $form.attr('action','/admin/actions/entry-export/export');
        $form.attr('method','POST');
        $form.attr('target','_blank');

        for(let key in data){
            if(typeof data[key] === 'object'){
                for(let key2 in data[key]){
                    const field = $('<input>');
                    field.attr('name', key + '['+key2+']');
                    field.val(data[key][key2]);
                    $form.append(field);
                }
            } else {
                const field = $('<input>');
                field.attr('name', key);
                field.val(data[key]);
                $form.append(field);
            }
        }

        $("body").append($form);
        $form.submit();
        
        // $.post(, data, function(retData){
        //     $("body").append("<iframe src='" + retData.url+ "' style='display: none;' ></iframe>");
        // });
    }
}
