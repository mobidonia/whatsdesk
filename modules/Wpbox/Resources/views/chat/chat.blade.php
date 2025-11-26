<div class="theChatHolder card border rounded-0 ">
    <div class="card-header" id="theChatHeader">
        <div class="d-flex justify-content-between align-items-center">

            <div v-cloak>

                <div class="d-flex align-items-center" style="gap:1rem" v-cloak>
                    <button @click="showConversations" v-cloak v-if="mobileChat" class="btn btn-icon ">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="#2dce89" class="w-6 h-6"
                            style="width: 20px; height:20px">
                            <path fill-rule="evenodd"
                                d="M9.53 2.47a.75.75 0 010 1.06L4.81 8.25H15a6.75 6.75 0 010 13.5h-3a.75.75 0 010-1.5h3a5.25 5.25 0 100-10.5H4.81l4.72 4.72a.75.75 0 11-1.06 1.06l-6-6a.75.75 0 010-1.06l6-6a.75.75 0 011.06 0z"
                                clip-rule="evenodd" />
                        </svg>

                    </button>
                    
                        
                    
                    <a :href="'/contacts/contacts/'+activeChat.id+'/edit'" class="profile-picture-container position-relative">
                        <div v-cloak v-if="activeChat&&activeChat.name&&activeChat.name[0]&&(activeChat.avatar==''||activeChat.avatar==null)"
                            class="avatar avatar-content bg-gradient-success" style="min-width:48px">@{{activeChat.name[0]}}
                        </div>
                        <img v-cloak v-if="activeChat&&(activeChat.avatar!=''&&activeChat.avatar!=null)"  alt="" :src="activeChat.avatar"
                            :data-src="activeChat.avatar" class="avatar" />
                            
                        <span  id="userCountry" v-if="activeChat&&activeChat.country" :class="'fi-'+activeChat.country.iso2.toLowerCase()" class="fi  fis flag-icon"></span>
                        <b-tooltip  target="userCountry">@{{activeChat.country.name}}</b-tooltip>
                        
                    </a>
                    <div class="d-flex flex-column">
                        <a  class="d-flex align-items-center" :href="'/contacts/contacts/'+activeChat.id+'/edit'">
                            <h3 class="mb-0 d-block text-nowrap">@{{activeChat.name}} <span class="ml-2 text-xs badge badge-pill hide-onmobile" :class="(getReplyNotification(activeChat)).class"><small>@{{ (getReplyNotification(activeChat)).text }}</small></span></h3>
                        </a>
                        <span class="text-xs text-dark opacity-6">@{{activeChat.phone}}</span>
                        
                       

                        
                       
                    </div>

                
                </div>
            </div>

            @include('wpbox::chat.actions')
        </div>
    </div>
    <div class="card-body overflow-auto overflow-x-hidden scrollable-div" ref="scrollableDiv" id="chatMessages">
        @include('wpbox::chat.message')
    </div>
    @include('wpbox::chat.tools')
</div>
