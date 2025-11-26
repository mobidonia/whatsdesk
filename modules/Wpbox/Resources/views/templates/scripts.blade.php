<script>
    "use strict";
    var templateManager=null;

    window.onload = function () {
        Vue.config.devtools=true;
        
        templateManager = new Vue({
        el: '#template_creator',
            data: {
                "template_name": "",
                "category": "MARKETING",
                "language": "en_US",
                "headerType": "none",
                "headerHandle": "",
                "headerImage": "",
                "headerVideo": "",
                "headerPdf": "",
                "headerText": "",
                "headerExampleVariable": "",
                "bodyText": "",
                "bodyExampleVariable": [],
                "footerText": "",
                "quickReplies": [],
                "vistiWebsite": [],
                "hasPhone": false,
                "dialCode": "",
                "phoneNumber": "",
                "callPhoneButtonText": "",
                "copyOfferCode":false,
                "offerCode":"",
                "dynamicUrls": [],
                "isSending":false
            },
            watch: {
                template_name: function (newVal, oldVal) {
                    //Don't allow spaces in the template name
                    this.template_name = newVal.replace(/\s/g, '_');    
                    
                    //Replace - with _
                    this.template_name = this.template_name.replace(/-/g, '_');

                    //Replace Capital letters with lowercase
                    this.template_name = this.template_name.toLowerCase();
                }
            },
            methods: {
                addHeaderVariable: function () {
                    if(!this.headervariableAdded){
                        this.headerText += ' \{\{1\}\}';
                    } 
                },
                addBold: function () {
                    this.bodyText += '*ENTER_CONTENT_HERE*';
                },
                addItalic: function () {
                    this.bodyText += '_ENTER_CONTENT_HERE_';
                },
                addCode: function () {
                    this.bodyText += '```ENTER_CODE_HERE```';
                },
                addVariable: function () {
                    // Add a variable to the body text
                    // First, get the next variable number
                    let nextVariable = this.bodyText.match(/\{\{[0-9]+\}\}/g);
                    if(nextVariable){
                        nextVariable = parseInt(nextVariable.pop().replace(/\{|\}/g, '')) + 1;
                    } else {
                        nextVariable = 1;
                    }
                    
                    this.bodyText += '\{\{'+nextVariable+'\}\}';
                },
                addQuickReply: function () {
                   this.quickReplies.push("");
                },
                deleteQuickReply: function (index) {
                    this.quickReplies.splice(index, 1);
                },
                addVisitWebsite: function () {
                    if(this.vistiWebsite.length >= 2){
                        return;
                    }
                    this.vistiWebsite.push({title: "", url: ""});
                },
                deleteVisitWebsite: function (index) {
                    this.vistiWebsite.splice(index, 1);
                },
                addCallPhone: function () {
                    this.hasPhone = true;
                },
                deletePhone: function () {
                    this.hasPhone = false;
                },
                addCopyOfferCode: function () {
                    this.copyOfferCode = true;
                },
                deleteCopyOfferCode: function () {
                    this.copyOfferCode = false;
                },
                addDynamicUrl: function () {
                    if(this.dynamicUrls.length >= 2){
                        return;
                    }
                    this.dynamicUrls.push({type: "dynamic", text: "", url: "", example: ""});
                },
                deleteDynamicUrl: function (index) {
                    this.dynamicUrls.splice(index, 1);
                },
                handleImageUpload: function (event) {
                    const formData = new FormData();
                    formData.append('imageupload', event.target.files[0]);
                    axios.post('/templates/upload-image', formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    }).then(response => {
                        if(response.data.status=='success'){
                            this.headerImage = response.data.url;
                            this.headerHandle = response.data.handle;
                        }else{
                            alert(response.data.response);
                        }
                    })
                },
                handleVideoUpload: function (event) {
                    const formData = new FormData();
                    formData.append('videoupload', event.target.files[0]);
                    axios.post('/templates/upload-video', formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    }).then(response => {
                        console.log('File uploaded successfully', response);
                        console.log(response.data);
                        this.headerVideo = response.data.url;
                        this.headerHandle = response.data.handle;
                    })
                },
                handlePdfUpload: function (event) {
                    const formData = new FormData();
                    formData.append('pdfupload', event.target.files[0]);
                    axios.post('/templates/upload-pdf', formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    }).then(response => {
                        console.log('File uploaded successfully', response);
                        this.headerPdf = response.data.url;
                        this.headerHandle = response.data.handle;
                    })
                },
                showDisabledInDemo: function () {
                    console.log('This feature is disabled in the demo');
                    alert('This feature is disabled in the demo');
                },
                submitTemplate: function () {
                    if(!this.isSending){
                        this.isSending=true;
                        var metaTemplate={
                            "name":this.template_name,
                            "category":this.category,
                            "language":this.language,
                            "allow_category_change":true,
                        };

                        var components=[];

                        //Add the body component
                        var bodyComponent={
                            "type":"BODY",
                            "text":this.bodyText
                        };
                        components.push(bodyComponent);

                        if(this.bodyVariables){
                            bodyComponent.example={
                                "body_text":[this.bodyExampleVariable]
                            };
                        }

                        //Header 
                        if(this.headerType == 'text'){
                            //Text
                            var headerComponent={
                                "type":"HEADER",
                                "format": "TEXT",
                                "text":this.headerText
                            };
                            if(this.headerExampleVariable){
                                headerComponent.example={
                                    "header_text":[this.headerExampleVariable]
                                };
                            }
                            components.push(headerComponent); 
                        }else if(this.headerType == 'image'){
                            //Header Image
                            var headerComponent={
                                "type":"HEADER",
                                "format": "IMAGE",
                                "example": {
                                    "header_handle":[ 
                                        this.headerHandle
                                    ]
                                }
                            };
                            components.push(headerComponent);
                        }else if(this.headerType == 'video'){
                            //Header Video
                            var headerComponent={
                                "type":"HEADER",
                                "format": "VIDEO",
                                "example": {
                                    "header_handle":[
                                        this.headerHandle
                                    ]
                                }
                            };
                            components.push(headerComponent);
                        } else if(this.headerType == 'pdf'){
                            //Header PDF
                            var headerComponent={
                                "type":"HEADER",
                                "format": "DOCUMENT",
                                "example": {
                                    "header_handle":[
                                        this.headerHandle
                                    ]
                                }
                            };
                            components.push(headerComponent);
                        } else if(this.headerType == 'none'){
                            //No Header

                        }

                        //Footer Text
                        if(this.footerText.length > 0){
                            var footerComponent={
                                "type":"FOOTER",
                                "text":this.footerText
                            };
                            components.push(footerComponent);
                        }

                        //Buttons
                        var buttonsComponent={
                            "type":"BUTTONS",
                            "buttons":[]
                        };

                        //Quick Replies
                        if(this.quickReplies.length > 0){
                            this.quickReplies.forEach(function (reply) {
                                buttonsComponent.buttons.push({
                                    "type":"QUICK_REPLY",
                                    "text":reply
                                });
                            });
                        }

                        //Visit Website
                        if(this.vistiWebsite.length > 0){
                            this.vistiWebsite.forEach(function (website) {
                                buttonsComponent.buttons.push({
                                    "type":"URL",
                                    "text":website.title,
                                    "url":website.url
                                });
                            });
                        }

                        //Call Phone
                        if(this.hasPhone){
                            buttonsComponent.buttons.push({
                                "type":"PHONE_NUMBER",
                                "text":this.callPhoneButtonText,
                                "phone_number":this.dialCode+this.phoneNumber
                            });
                        }

                        //Copy Offer Code
                        if(this.copyOfferCode){
                            buttonsComponent.buttons.push({
                                "type":"COPY_CODE",
                                "example":this.offerCode
                            });
                        }

                        //Dynamic URLs
                        if(this.dynamicUrls.length > 0){
                            this.dynamicUrls.forEach(function (dynamicUrl) {
                                buttonsComponent.buttons.push({
                                    "type":"URL",
                                    "text": dynamicUrl.text,
                                    "url": dynamicUrl.url,
                                    "example": {
                                        "url": [dynamicUrl.example]
                                    }
                                });
                            });
                        }

                        //Add buttons to the components
                        if(buttonsComponent.buttons.length > 0){
                            components.push(buttonsComponent);
                        }
                        

                        //Add the components to the metaTemplate
                        metaTemplate.components=components;

                

                        console.log(metaTemplate);



                        //Send the metaTemplate to the server
                        axios.post('/templates/submit',metaTemplate)
                            .then(function (response) {
                                // Handle success callback
                                console.log(response);

                                if(response.data.status=="success"){
                                    //Redirect to the template list
                                    window.location.href = '/templates?ok=1&refresh=yes';
                                }else{
                                    alert('An error occurred while submitting the template. Check the console.')
                                }
                               
                                
                                
                            })
                            .catch(function (error) {
                                // Handle error callback
                                console.log(error);
                                alert('An error occurred while submitting the template. Check the console.')
                               // window.location.href = '/templates?ok=0&refresh=yes';
                            });
                    }
                }
            },
            computed: {
                headervariableAdded: function () {
                    return this.headerText.indexOf('\{\{1\}\}') > -1 && this.headerType == 'text';
                },
                bodyVariables: function () {
                    return this.bodyText.match(/\{\{[0-9]+\}\}/g);
                },
                bodyReplacedWithExample: function () {
                    let bodyText = this.bodyText;
                    if(this.bodyExampleVariable){
                        this.bodyExampleVariable.forEach(function (example, index) {
                            bodyText = bodyText.replace('\{\{'+(index+1)+'\}\}', example);
                        });
                    }
                    return bodyText;
                },
                headerReplacedWithExample: function () {
                    let headerText = this.headerText;
                    if(this.headerExampleVariable){
                        headerText = headerText.replace('\{\{1\}\}', this.headerExampleVariable);
                    }
                    return headerText;
                }
            }
        });
    };
</script>