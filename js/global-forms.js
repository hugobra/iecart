/* class used on dispayed errors */
var errorCls = 'error';

/* helpers to manage error display on form validation */
var addFormError = function(e) {
    box.dom('#' + e.source.id + 'Error').html('<p>' + e.source.msg + '</p>');
};

/* @return Element where error have to be displayed */
var getErrorTarget = function(field) {
    var errorTarget;
    if (field.type == 'radio') {
        errorTarget = field.dom.eq(0).closest('.radios').children('.legend');
    } else if (field.type == 'checkbox') {
        errorTarget = field.dom.eq(0).next('label');
    } else {
        errorTarget = field.getLabel();
    }
    return errorTarget;
};

/* Add an error message on a field */
var addFieldError = function(e) {
    getErrorTarget(e.source).parent().addClass('error');
};

/* Remove an error message on a field */
var removeFieldError = function(e) {
    getErrorTarget(e.source).parent().removeClass('error');
};


/* wait for the DOM to be ready */
box.dom(document).ready(function() {

    $('.forms').find('form').each(function(){
        var _this = $(this);
        box.get('ui').create('form.'+_this.attr('id'), {
            rootElm: '#'+_this.attr("id")
        }).addReplacement().mustValidate(function(form) {
            form.dom.find('.select select').each(function() {
                var _this = this;
                if($(_this).data('mandatory') === true) {
                    form.field(_this.id).mustValidate(function(field) {
                        if (field.getValue() == '0') {
                            return box.get('l10n:errors').required;
                        }
                    });
                }
            });

            form.dom.find('.text input').each(function() {
                var _this = this;
                if($(_this).data('mandatory') === true) {
                    form.field(_this.id).mustValidate(function(field) {
                        if (field.isEmpty()) {
                            return box.get('l10n:errors').required;
                        }
                    });
                }
            });

            form.dom.find('.radios input').each(function() {
                var _this = this;
                if($(_this).data('mandatory') === true) {
                    form.field(_this.id).mustValidate(function(field) {
                         if (!field.isChecked()) {
                            return box.get('l10n:errors').required;
                        }
                    });
                }
            });

            return box.get('l10n:errors').forms;
        });
    });

    $('.checkbox-list-wrapper').find('.first-level > .checkbox label').click(function(){
        var _this = this;
        for(var i=0; i<$(_this).parent().siblings(".second-level").find(' .checkbox input').length;++i){
            if($(_this).hasClass("checked")){
                box.get('ui:form.form-checkboxlist').field($(_this).parent().siblings(".second-level").find(' .checkbox input')[i].id).uncheck();
            }else{
                box.get('ui:form.form-checkboxlist').field($(_this).parent().siblings(".second-level").find(' .checkbox input')[i].id).check();
            }
            
        }
        
    });

    $('.more-options').click(function(e){
        e.preventDefault();
        $('.first-level.hidden').toggleClass("visible");
    });
    
    /* React to notifications */
    box.subscribe(
        /* For all fields */
        { name: 'error>ui:field', handler: addFieldError },
        { name: 'valid>ui:field', handler: removeFieldError },
        
        /* For all forms */
        {
            name: 'submit>ui:form',
            handler: function(e) {
                if (e.data.valid === false) {
                    addFormError(e);
                }
            }
        }
    );
});