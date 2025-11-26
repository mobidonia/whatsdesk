  <!-- Temmplate Details -->
  <div class="col-xl-5 mt-2">
    <div class="card shadow">
        <div class="card-header bg-white border-0">
            <div class="row align-items-center">
                <div class="col-8">
                    <h3 class="mb-0">{{__('Form')}}</h3>
                </div>
            </div>
        </div>
        <div class="card-body">
           <!-- Template header - None, Text, Media -->
            <div class="form-group">
                <label for="header"><strong>{{__('Header type')}}</strong></label>
                <span class="badge badge-primary" style="color:#8898aa">{{ __('Optional')}}</span><br />
                <small>{{__('Add a title or choose which type of media you will use for this header.')}}</small>
                <select name="header" id="header" class="form-control" v-model="headerType">
                    <option value="none">{{__('None')}}</option>
                    <option value="text">{{__('Text')}}</option>
                    <option value="image">{{__('Image')}}</option>
                    <option value="video">{{__('Video')}}</option>
                    <option value="pdf">{{__('PDF')}}</option>
                </select>
            </div>

            <!-- Templpate header text -->
            <div v-if="headerType=='text'" class="form-group">
                <label for="header_text"><strong>{{__('Header text')}}</strong></label>
                <div class="input-group">
                    <input v-model="headerText" type="text" name="header_text" id="header_text" class="form-control" placeholder="{{__('Header text')}}" value="{{ old('header_text') }}">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-outline-primary btn-sm" @click="addHeaderVariable()">{{__('Add variable')}}</button>
                    </div>
                </div>

                <div class="mt-2">
                    <small>{{__('You can use variables to personalize the header text.')}}</small>
                </div>

                <div class="mt-2" v-if="headervariableAdded">
                    <div class="form-group p-4 "  style="background-color: #e9ecef; !important">
                        <label for="headerExampleVariable"><strong>{{__('Samples for header content')}}</strong></label>
                        <br /><small>{{ __('To help us review your content, provide examples of the variables in the header.')}}</small>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                
                                <span class="input-group-text" id="basic-addon1">@{{ '{' }}{1}@{{ '}' }}</span>
                            </div>
                            <input v-model="headerExampleVariable" type="text" class="form-control" placeholder="{{ __('Enter content for the heder variable')}}" aria-describedby="basic-addon1">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Template header image -->
            <div v-if="headerType=='image'" class="form-group">
                <label for="header_image"><strong>{{__('Header image')}}</strong></label>
                <input @change="handleImageUpload" type="file"  accept="image/*" name="header_image" id="header_image" class="form-control" placeholder="{{__('Header image')}}" value="{{ old('header_image') }}">
            </div>

            <!-- Template header video -->
            <div v-if="headerType=='video'" class="form-group">
                <label for="header_video"><strong>{{__('Header video')}}</strong></label>
                <input @change="handleVideoUpload" type="file" accept="video/*" name="header_video" id="header_video" class="form-control" placeholder="{{__('Header video')}}" value="{{ old('header_video') }}">
            </div>

            <!-- Template header pdf -->
            <div v-if="headerType=='pdf'" class="form-group">
                <label for="header_pdf"><strong>{{__('Header pdf')}}</strong></label>
                <input type="file" accept="application/pdf" name="header_pdf" id="header_pdf" class="form-control" placeholder="{{__('Header pdf')}}" value="{{ old('header_pdf') }}">
            </div>

            <hr />


            <!-- Body -->
            <div class="form-group">
                <label for="body"><strong>{{__('Body')}}</strong></label>
                <span class="badge badge-primary" style="color:#8898aa">{{ __('Required')}}</span>
                <p class="small">{{__('Enter the text for your message in the language you have selected.')}}</p>
                <textarea rows="5" v-model="bodyText" name="body" id="body" class="form-control" placeholder="{{__('Body')}}" value="{{ old('body') }}"></textarea>
                <div class="text-right mt-4">
                    <button @click="addBold()" class="btn btn-outline-secondary btn-sm mx-2" type="button" title="Bold">
                        <strong>B</strong>
                    </button>
                    <button @click="addItalic()" class="btn btn-outline-secondary btn-sm mx-2" type="button" title="Italic">
                        <em>I</em>
                    </button>
                    <button @click="addCode()" class="btn btn-outline-secondary btn-sm mx-2" type="button" title="Code">
                        <code>&lt;&gt;</code>
                    </button>
                    <button @click="addVariable()" class="btn btn-secondary btn-sm mx-2" type="button">
                        {{ __('Add variable')}}
                    </button>
                </div>
                <div class="mt-2" v-if="bodyVariables">
                    <div class="form-group p-4 "  style="background-color: #e9ecef; !important">
                        <label for="headerExampleVariable"><strong>{{__('Samples for body content')}}</strong></label>
                        <br /><small>{{ __('To help us review your content, provide examples of the variables in the body.')}}</small>
                        <div v-for="(v, index) in bodyVariables" class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1">@{{v}}</span>
                            </div>
                            <input v-model="bodyExampleVariable[index]" type="text" class="form-control" placeholder="{{ __('Enter content for the variable')}}" aria-describedby="basic-addon1">
                        </div>
                    </div>
                </div>
            </div>

            <hr />

            <!-- Footer -->
            <div class="form-group">
                <label for="footer"><strong>{{__('Footer')}}</strong></label>
                <span class="badge badge-primary" style="color:#8898aa">{{ __('Optional')}}</span>
                <p class="small">{{__('Enter the text for your footer in the language you have selected.')}}</p>
                <input v-model="footerText" type="text" name="footer" id="footer" class="form-control" placeholder="{{__('Footer')}}" value="{{ old('footer') }}">
            </div>

            <hr />
            
            <!-- Quick Reply Buttonns -->
            <div class="form-group">
                <label for="footer"><strong>{{__('Quick Reply Buttons')}}</strong></label>
                <span class="badge badge-primary" style="color:#8898aa">{{ __('Optional')}}</span>
                <p class="small">{{__('Create buttons that let customers respond to your message')}}</p>
                
                <!-- Add the button -->
                <div class="text-right mt-2">
                    <button @click="addQuickReply()" class="btn btn-outline-primary btn-sm" type="button">
                        <span class="btn-inner--icon">{{ __('Add Quick Reply')}}</span>
                    </button> 
                </div>
                <div class="mt-2" v-if="quickReplies.length>0">
                    <div class="form-group p-4 "  style="background-color: #e9ecef; !important">
                        <label><strong>{{__('Quick Reply buttons')}}</strong></label>
                        <div v-for="(v, index) in quickReplies" class="form-group">
                            <div class="input-groups">
                                
                                <div class="row">
                                    <div class="col-10">
                                        <input v-model="quickReplies[index]" type="text" class="form-control mr-4 pr-4" placeholder="{{ __('Button text') }}">
                                    </div>
                                    <div class="col-2 mt-2">
                                        <button type="button" class="btn btn-outline-secondary btn-sm" @click="deleteQuickReply(index)">
                                            <span class="btn-inner--icon"> X </span>
                                        </button>
                                    </div>
                                       
                                </div>
                                
                                
                            </div>
                        </div>
                    </div>
                </div>
                
                
            </div>

            <hr />
            
            <!-- Call to Action Buttonns -->
            <div class="form-group">
                <label for="footer"><strong>{{__('Call to Action Buttons')}}</strong></label>
                <span class="badge badge-primary" style="color:#8898aa">{{ __('Optional')}}</span>
                <p class="small">{{__('Create buttons that let customers take action')}}</p>
                <!-- Add the button -->
                <div class="text-right mt-2">
                    <button :disabled="vistiWebsite.length>1" @click="addVisitWebsite()" class="btn btn-outline-primary btn-sm" type="button">
                        <span class="btn-inner--icon">{{ __('Visit website - x2')}}</span>
                    </button> 
                    <button v-if="!hasPhone" @click="addCallPhone()" class="btn btn-outline-primary btn-sm" type="button">
                        <span class="btn-inner--icon">{{ __('Call phone number')}}</span>
                    </button> 
                    <button v-if="hasPhone" @click="deletePhone()" class="btn btn-outline-danger btn-sm" type="button">
                        <span class="btn-inner--icon">{{ __('Remove phone number')}}</span>
                    </button> 
                    <button v-if="!copyOfferCode" @click="addCopyOfferCode()" class="btn btn-outline-primary btn-sm" type="button">
                        <span class="btn-inner--icon">{{ __('Copy offer code')}}</span>
                    </button> 
                    <button v-if="copyOfferCode" @click="deleteCopyOfferCode()" class="btn btn-outline-danger btn-sm" type="button">
                        <span class="btn-inner--icon">{{ __('Remove offer code')}}</span>
                    </button>
                    <button :disabled="dynamicUrls.length>1" @click="addDynamicUrl()" class="btn btn-outline-primary btn-sm" type="button">
                        <span class="btn-inner--icon">{{ __('Visit dynamic URL')}}</span>
                    </button>
                </div>
            </div>

            <div class="mt-2" v-if="vistiWebsite.length>0">
                <div class="form-group p-4 "  style="background-color: #e9ecef; !important">
                    <label><strong>{{__('Visit Website buttons')}}</strong></label>
                    <div v-for="(v, index) in vistiWebsite" class="form-group">
                        <div class="input-groups">
                            
                            <div class="row">
                                <div class="col-4">
                                    <input v-model="vistiWebsite[index]['title']" type="text" class="form-control" placeholder="{{ __('Button text') }}">
                                </div>
                                <div class="col-7">  
                                    <input v-model="vistiWebsite[index]['url']" type="text" class="form-control" placeholder="{{ __('URL') }}">
                                </div>
                                <div class="col-1 mt-2">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" @click="deleteVisitWebsite(index)">
                                        <span class="btn-inner--icon"> X </span>
                                    </button>
                                </div>
                                   
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Call phone number -->
            <div class="mt-2" v-if="hasPhone">
                <div class="form-group p-4 "  style="background-color: #e9ecef; !important">
                    <div class="form-group">
                        <label for="call_phone"><strong>{{__('Call phone number')}}</strong></label>
                        <div class="input-group">
                            <input v-model="callPhoneButtonText" type="text" name="call_phone_name" id="call_phone_name" class="form-control" placeholder="{{__('Button name')}}" value="{{ old('call_phone_name') }}">
                        </div>
                        <div class="input-group mt-2">
                            <input v-model="dialCode" type="text" name="call_phone_dial_code" id="call_phone_dial_code" class="form-control" placeholder="{{__('Dial code')}}" value="{{ old('call_phone_dial_code') }}">
                            <input v-model="phoneNumber" type="text" name="call_phone_number" id="call_phone_number" class="form-control" placeholder="{{__('Phone number')}}" value="{{ old('call_phone_number') }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Copy offer code -->
            <div class="mt-2" v-if="copyOfferCode">
                <div class="form-group p-4 "  style="background-color: #e9ecef; !important">
                    <div class="form-group">
                        <label for="offer_code"><strong>{{__('Offer code button')}}</strong></label>
                        <div class="input-group">
                            <input v-model="offerCode" type="text" name="offer_code" id="offer_code" class="form-control" placeholder="{{__('Offer code sample')}}" value="{{ old('offer_code') }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dynamic URLs -->
            <div class="mt-2" v-if="dynamicUrls.length>0">
                <div class="form-group p-4" style="background-color: #e9ecef; !important">
                    <label><strong>{{__('Dynamic URL buttons')}}</strong></label>
                    <div v-for="(v, index) in dynamicUrls" class="form-group">
                        <div class="input-groups">
                            <div class="row">
                                <div class="col-4">
                                    <select v-model="dynamicUrls[index]['type']" class="form-control">
                                        <option value="dynamic">{{__('Dynamic')}}</option>
                                    </select>
                                </div>
                                <div class="col-3">
                                    <input v-model="dynamicUrls[index]['text']" type="text" class="form-control" placeholder="{{ __('Button text') }}">
                                </div>
                                <div class="col-4">
                                    <input v-model="dynamicUrls[index]['url']" type="text" class="form-control" placeholder="{{ __('URL with dynamic variable') }}">
                                </div>
                                <div class="col-1 mt-2">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" @click="deleteDynamicUrl(index)">
                                        <span class="btn-inner--icon"> X </span>
                                    </button>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label><strong>{{__('Sample URL')}}</strong></label>
                                        <input v-model="dynamicUrls[index]['example']" type="text" class="form-control" placeholder="{{ __('Example: http://site.com/?id=12') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>



            

           
        </div>
    </div>
</div>