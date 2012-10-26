Qconfig = Class.create();
Qconfig.prototype = {
    initialize:function (url, website, store, section) {
        this.url = url;
        this.website = website;
        this.store = store;
        this.section = section;
        this.request = null;
        this.timeout = null;
        this.timeout_delay = 400;
    },
    onkeyup:function (box) {
        $$('.treynolds_qconfig_box').each(function (sync_box) {
            if (sync_box != box) {
                sync_box.setValue(box.getValue());
            }

        });
        if (this.timeout != null) {
            clearTimeout(this.timeout);
        }
        this.timeout = setTimeout(this.ontimeout.bind(this), this.timeout_delay);
    },
    ontimeout:function () {
        this.timeout = null;

        if (this.request != null) {
            this.request.abort();
        }
        var query_string = $('treynolds_qconfig_box').getValue().strip();
        if (query_string.length == 0) {
            this.clear_searching();
            return;
        }
        $$('.treynolds_qconfig_loading').each(
            function(elm){
                elm.addClassName('treynolds_loading');
            }
        );
        new Ajax.Request(this.url, {
                method:'get',
                loaderArea:false,
                parameters:{
                    qsearch:query_string,
                    section:this.section,
                    website:this.website,
                    store:this.store
                },
                onSuccess:this.onsuccess.bind(this)

            }
        );
    },
    onsuccess:function (transport) {
        this.handle_success(transport.responseJSON);
    },
    handle_success:function (data) {
        this.clear_searching();
        $$('#system_config_tabs, .entry-edit').each(
            function (elm) {
                elm.addClassName('treynolds_searching');
            }
        );

        if(data.nav.length == 0 && data.group.length == 0 && data.field.length == 0){
            this.handle_no_results()
            return;
        }

        for (var i = 0; i < data.nav.length; i++) {
            $$('#system_config_tabs a[href*="' + data.nav[i] + '"]').each(
                function (elm) {
                    elm.up().addClassName('treynolds_active');
                }
            );
        }
        var tmp = null;
        if (data.group.length > 0) {
            for (i = 0; i < data.group.length; i++) {
                tmp = $(data.group[i] + '-head');
                if (tmp != null) {
                    tmp.up().addClassName('treynolds_active');
                }
            }
        }
        if (data.field.length > 0) {
            for (i = 0; i < data.field.length; i++) {
                tmp = $(data.field[i]);
                if (tmp != null) {
                    tmp.addClassName('treynolds_active');
                }
            }
        }

        $$('.treynolds_active').each(function (t) {

            if (t.previous() != null && t.previous().hasClassName('treynolds_active')) {
                t.addClassName('treynolds_bottom');
            }
            if (t.next() != null && t.next().hasClassName('treynolds_active')) {
                t.addClassName('treynolds_top');
            }
            if(t.hasClassName('entry-edit-head')){
                var count = t.up().select('.form-list .treynolds_active').length ;
                var span = new Element('span', {'class':'treynolds_qconfig_field_count'}).update(count + ' Field'+(count==1?' Matches':'s Match'));
                t.select('a')[0].insert(span);
            }
        });
    },
    onescape:function(){
        //Don't want a request coming back after we clear.
        if(this.timeout != null){
            clearTimeout(this.timeout);
            this.timeout = null;
        }
        $$('.treynolds_qconfig_box').each(function(box){
            box.setValue('');
        });
        this.clear_searching();
    },
    handle_no_results:function(){
        $$('.treynolds_qconfig_box_wrap').each(function(noresults){
            noresults.addClassName('no_results');
        });
    },
    clear_searching:function () {
        $$('.treynolds_loading').each(
            function (elm){
                elm.removeClassName('treynolds_loading');
            }
        );
        $$('.no_results').each(
            function (elm){
                elm.removeClassName('no_results');
            }
        );
        $$('.treynolds_active').each(
            function (elm) {
                elm.removeClassName('treynolds_active');
                elm.removeClassName('treynolds_top');
                elm.removeClassName('treynolds_bottom');
            }
        );
        $$('.treynolds_searching').each(
            function (elm) {
                elm.removeClassName('treynolds_searching');
            }
        );
        $$('.treynolds_qconfig_field_count, b.treynolds_nav_count').each(
            function(elm){
                elm.remove();
            }
        );
    }
};
