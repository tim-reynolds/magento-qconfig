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
        });
    },
    clear_searching:function () {
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
    }
};
