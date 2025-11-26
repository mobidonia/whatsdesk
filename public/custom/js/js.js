"use strict";

var js = {
    document_ready : function() {
      js.cookieDialog();
      js.datepicker();
    },
    
    cookieDialog(){
      setTimeout(function(){$('#allow_cookies').fadeIn(400);}, 3000);
    },
    trans(originalKey, replace = {})
    {
    
        key="custom."+originalKey;
        let translation = key.split('.').reduce((t, i) => t&&t[i] || null, window.translations);

        for (var placeholder in replace) {
            translation = translation.replace(`:${placeholder}`, replace[placeholder]);
        }
        if(translation==null){
          return originalKey;
        }

        return translation;
    },
    initializeGoogle:function(){
      var input = document.getElementById('txtlocation');
      if(input){
        var autocomplete = new google.maps.places.Autocomplete(input, {  });

        google.maps.event.addListener(autocomplete, 'place_changed', function () {
            var place = autocomplete.getPlace();

            IsplaceChange = true;
        });
      }
      
    },
    notify:function(text, type){
      var color="#4fd69c";
      var autoHideDelay=5000;
      var autoHide= true;

      if(type=="warning"){
        color="#fc7c5f";
      }else if(type=="primary"){
        color="#37d5f2";
        autoHide=false;
      }else if(type=="danger"){
        color="#ff0000";
        autoHide=false;
      }
      $.notify.addStyle('custom', {
          html: "<div><strong><span data-notify-text /></strong></div>",
          classes: {
              base: {
                  "position": "relative",
                  "margin-bottom": "1rem",
                  "padding": "1rem 1.5rem",
                  "border": "1px solid transparent",
                  "border-radius": ".375rem",

                  "color": "#fff",
                  "border-color": color,
                  "background-color": color,
              },
              success: {
                  "color": "#fff",
                  "border-color": color,
                  "background-color": color,
              }
          }
          });

          $.notify(text,{
              position: "bottom right",
              style: 'custom',
              className: 'success',
              autoHideDelay: autoHideDelay,
              autoHide:autoHide,
          }
      );
    },
    initStripe: function(STRIPE_KEY,FORM_NAME){
      
      /**
       * STRIPE_KEY -- The stripe key
       * FORM_NAME -- The form name--  ex. 'stripe-payment-form'
       *  
       */


      

      // Create a Stripe client.
      var stripe = Stripe(STRIPE_KEY);

      // Create an instance of Elements.
      var elements = stripe.elements();

    // Custom styling can be passed to options when creating an Element.
    // (Note that this demo uses a wider set of styles than the guide below.)
    var style = {
        base: {
            color: '#32325d',
            fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
            fontSmoothing: 'antialiased',
            fontSize: '16px',
            '::placeholder': {
            color: '#aab7c4'
            }
        },
        invalid: {
            color: '#fa755a',
            iconColor: '#fa755a'
        }
    };

    // Create an instance of the card Element.
    var card = elements.create('card', {style: style});

    // Add an instance of the card Element into the `card-element` <div>.
    card.mount('#card-element');

    // Handle real-time validation errors from the card Element.
    card.addEventListener('change', function(event) {
        var displayError = document.getElementById('card-errors');
        if (event.error) {
            displayError.textContent = event.error.message;
        } else {
            displayError.textContent = '';
        }
    });

    const cardHolderName = document.getElementById('name');

    // Handle form submission  - for card.
    var form = document.getElementById(FORM_NAME);
    form.addEventListener('submit', async function(event) {
        event.preventDefault();

        //IF delivery - we need to have selected address
        if(validateOrderFormSubmit()){
            const { paymentMethod, error } = await stripe.createPaymentMethod(
                'card', card, {
                    billing_details: { name: cardHolderName.value }
                }
            );

            if (error) {
                // Display "error.message" to the user...
                alert(error.message);
            } else {
                var hiddenInput = document.createElement('input');
                hiddenInput.setAttribute('type', 'hidden');
                hiddenInput.setAttribute('name', 'stripePaymentId');
                hiddenInput.setAttribute('value', paymentMethod.id);
                form.appendChild(hiddenInput);
        
                // Submit the form
                form.submit();
            }
        }
      })
    },
    datepicker : function(){
       
        $(".daterange").each(function(){
          var $this = $(this);
          var $end = moment();
          var $start = moment().subtract(1, 'month');
          $this.daterangepicker({
            autoUpdateInput: false,
            locale: {cancelLabel: 'Clear'},
            buttonClasses : "datepicker-btn",
            startDate : $start,
            endDate: $end,
            ranges: {
               'Today': [moment(), moment()],
               'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
               'Last 7 Days': [moment().subtract(6, 'days'), moment()],
               'Last 30 Days': [moment().subtract(29, 'days'), moment()],
               'This Month': [moment().startOf('month'), moment().endOf('month')],
               'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
               'This year': [moment().startOf('year'), moment()],
               'Last year': [moment().startOf('year').subtract(1, 'years'), moment().subtract(1, 'year')]
            }
          });
          $this.on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
          });
          $this.on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
          });
        });
      }
}

var $ = jQuery.noConflict();
$(window).on("load",function(){js.window_load?js.window_load():"";});
(function($) {
  "use strict";
  $(document).ready(function() {js.document_ready();});
})(jQuery);




Number.prototype.pad = function(size) {
  var s = String(this);
  while (s.length < (size || 2)) {s = "0" + s;}
  return s;
}
$.randomID = function(){ return ( Math.random().toString(36).substring(2) ) };
$.tpl = function(template, data){
  return template.replace(/\{([\w\.]*)\}/g, function(str, key) {
    var keys = key.split("."), v = data[keys.shift()];
    for (var i = 0, l = keys.length; i < l; i++) v = v[keys[i]];
    return (typeof v !== "undefined" && v !== null) ? v : "";
  });
};
$.expr[':'].contains = function(a, i, m) { return jQuery(a).text().toUpperCase().indexOf(m[3].toUpperCase()) >= 0; };