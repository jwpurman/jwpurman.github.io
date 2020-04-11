/**
 * Image up-loader functions
 */
var selector;
function upload_media_image(selector){
// ADD IMAGE LINK
    jQuery('body').on( 'click', selector , function( event ){
    event.preventDefault();

    var imgContainer = jQuery(this).closest('.attachment-media-view').find( '.thumbnail-image'),
    placeholder = jQuery(this).closest('.attachment-media-view').find( '.placeholder'),
    imgIdInput = jQuery(this).siblings('.upload-id');

    // Create a new media frame
    frame = wp.media({
        title: 'Select or Upload Image',
        button: {
        text: 'Use Image'
        },
        multiple: false  // Set to true to allow multiple files to be selected
    });

    // When an image is selected in the media frame...
    frame.on( 'select', function() {

    // Get media attachment details from the frame state
    var attachment = frame.state().get('selection').first().toJSON();

    // Send the attachment URL to our custom image input field.
    imgContainer.html( '<img src="'+attachment.url+'" style="max-width:100%;"/>' );
    placeholder.addClass('hidden');
    imgIdInput.val( attachment.url ).trigger('change');
    });

    // Finally, open the modal on click
    frame.open();
    
    });
}

function delete_media_image(selector){
    // DELETE IMAGE LINK
    jQuery('body').on( 'click', selector, function( event ){

    event.preventDefault();
    var imgContainer = jQuery(this).closest('.attachment-media-view').find( '.thumbnail-image'),
    placeholder = jQuery(this).closest('.attachment-media-view').find( '.placeholder'),
    imgIdInput = jQuery(this).siblings('.upload-id');

    // Clear out the preview image
    imgContainer.find('img').remove();
    placeholder.removeClass('hidden');

    // Delete the image id from the hidden input
    imgIdInput.val( '' ).trigger('change');

    });
}

jQuery(document).ready(function($){
    "use strict";
    
    /**
     * Image up-loader
     */
    upload_media_image('.mt-upload-button');
    delete_media_image('.mt-delete-button');
    
    /**
     * Widgets Active Callback Scripts
     */

    function field_relation_action( relation_field, relation, relations_key ){
        var relation_field_value = relation_field.val();
        for(var relation_key in relation){
            if(relations_key == "empty" || relations_key == "exist"){
                relation_field_value = relations_key;
            }else if(relations_key == "values"){
                if(relation_key != relation_field_value){
                    continue;
                }
            }
            var relation_details = relation[relation_key];
            for(var action_key in relation_details){
                var action_details = relation_details[action_key];
                var action_detail_class = action_details.join(", .");
                var action_class = '.'+action_detail_class;
                switch(action_key){
                    case 'show_fields':
                        relation_field.closest('.widget-content').find(action_class).slideDown( 180 );
                        relation_field.closest('.panel-dialog').find(action_class).slideDown( 180 );
                        break;
                    case 'hide_fields':
                        relation_field.closest('.widget-content').find(action_class).slideUp( 180 );
                        relation_field.closest('.panel-dialog').find(action_class).slideUp( 180 );
                        break;
                    default:
                        console.warn('Action ' + relation_key + ' case is not defined');
                    break;
                }
            }
        }
    }

    function field_relation( relation_field, relation_field_value, relations ){
        if(!relations){
            return;
        }
        for(var relations_key in relations){
            var relation = relations[relations_key];
            if(!relation){
                continue;
            }
            switch(relations_key){
                case 'exist':
                    if( relation_field_value ){
                        field_relation_action(relation_field, {'exist':relation}, relations_key);
                    }
                    break;
                case 'empty':
                    if( !relation_field_value ){
                        field_relation_action(relation_field, {'empty':relation}, relations_key);
                    }
                    break;
                case 'values':
                    if( relation ){
                        field_relation_action(relation_field, relation, relations_key);
                    }
                    break;
                default: 
                    console.warn('Relation key ' + relations_key + 'is not found.');
                    break;
            }
        }
    }

    function widgetUpdate( widget ) {
        $('.shopay_widget_field_relation').each(function(){
            var relation_field = $(this);
            var relation_relation_field_value = relation_field.val();
            var relations = relation_field.data('relations');
            if( !relation_relation_field_value && !relations ) {
                return;
            }
            field_relation( relation_field, relation_relation_field_value, relations );
        });
    }

    $('.shopay_widget_field_relation').each(function(){
        var relation_field = $(this);
        var relation_relation_field_value = relation_field.val();
        var relations = relation_field.data('relations');
        
        if( !relation_relation_field_value && !relations ) {
        return;
        }
        field_relation( relation_field, relation_relation_field_value, relations );

    });

    $('body').on( 'change', '.shopay_widget_field_relation', function() {
        var relation_field = $(this);
        var relation_relation_field_value = relation_field.val();
        var relations = relation_field.data('relations');
        if( !relation_relation_field_value && !relations ) {
        return;
        }
        field_relation( relation_field, relation_relation_field_value, relations );
    });

    $('.shopay_widget_field_relation').trigger('change');

    $( document ).on( 'widget-added widget-updated', function( e, widget ) {
        widgetUpdate( widget );
    });

    /**
     * Script for switch option
     */
    $('body').on( 'click', '.mt-widget-switch', function() {
        var $this = $(this);
        if( $this.hasClass('switch-on') ) {
            $(this).removeClass('switch-on');
            $this.next('input').val('off').trigger('change');
        } else {
            $(this).addClass('switch-on');
            $this.next('input').val('on').trigger('change');
        }
    });

    /**
     * Widget section toggle
     */
    $('body').on( 'click', '.group-title', function() {
        $(this).find('h4').toggleClass('open');
        $(this).next('.group-fields-wrapper').slideToggle("slow");
    });
});