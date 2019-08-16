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
        this.elementIndex = Object.assign({}, Craft.elementIndex);
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
    }
    
    exportEntries(format = 'xlsx') {

        this.updateSettings();
        
        const data = this.settings;
        data[window.Craft.csrfTokenName] = window.Craft.csrfTokenValue; 
        data.format = format;
        

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
    }
}
