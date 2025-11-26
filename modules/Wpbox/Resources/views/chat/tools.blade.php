<div class="card-footer d-block">
    <div>
        <b-tabs pills justified content-class="mt-0" class="m-2">
            <b-tab @click.prevent="switchChatTab('reply')" active title-link-class="small-tab" title-link-style="">
                <template #title>
                    💬 {{__('Reply')}}
                </template>
            </b-tab>
            <b-tab @click.prevent="switchChatTab('documents')" title-link-class="small-tab">
                <template #title>
                    📄 {{__('Documents')}}
                </template>
            </b-tab>
            <b-tab @click.prevent="switchChatTab('quick-replies')" title-link-class="small-tab">
                <template #title>
                    ⚡ {{__('Quick replies')}}
                </template>
            </b-tab>
           
            
        </b-tabs>
    </div>

    <div class="tab-content mb-0" id="chatTabsContent" >
        <div v-if="chatTab === 'reply'" class="tab-pane fade show active" id="reply" role="tabpanel" aria-labelledby="reply-tab">
            <div class="align-items-center">
                <div class="px-0 py-4 d-flex">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <button  v-if="!mobileChat" type="button" class="btn btn-outline-primary" id="emoji-btn" style="border-color:#cad1d7; border-right-color:transparent">
                                <svg fill="currentColor" width="24" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><path d="M464 256v0c0-114.88-93.13-208-208-208C141.12 48 48 141.12 48 256v0c0 114.87 93.12 208 208 208 114.87 0 208-93.13 208-208ZM0 256v0C0 114.61 114.61 0 256 0c141.38 0 256 114.61 256 256v0c0 141.38-114.62 256-256 256C114.61 512 0 397.38 0 256Zm130.7 57.9c-4.2-13.6 7.1-25.9 21.3-25.9h212.5c14.2 0 25.5 12.4 21.3 25.9C369 368.4 318.2 408 258.2 408c-60 0-110.8-39.6-127.5-94.1ZM144.4 192v0c0-17.68 14.32-32 32-32 17.67 0 32 14.32 32 32v0c0 17.67-14.33 32-32 32 -17.68 0-32-14.33-32-32Zm192-32v0c17.67 0 32 14.32 32 32 0 17.67-14.33 32-32 32v0c-17.68 0-32-14.33-32-32 0-17.68 14.32-32 32-32Z"/></svg>
                            </button>
                        </div>
                        <input @keyup.enter="sendChatMessage" v-model="activeMessage" type="text" id="message" class="form-control pl-2 primary" placeholder="{{ __('Type your message here') }}"
                            aria-label="{{ __('Message') }}">
                        
                    </div>
                    <div class="ml-2">
                        <button class="btn btn-success btn-icon rounded-circle" @click="sendChatMessage">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6" style="width: 20px; height:20px">
                                <path d="M3.478 2.405a.75.75 0 00-.926.94l2.432 7.905H13.5a.75.75 0 010 1.5H4.984l-2.432 7.905a.75.75 0 00.926.94 60.519 60.519 0 0018.445-8.986.75.75 0 000-1.218A60.517 60.517 0 003.478 2.405z" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Links tab -->
        @if ($company->getConfig('show_links_tab', true))
            <div>

                <div>
 
                    
                    <b-modal id="modal-link-fetcher" scrollable hide-backdrop content-class="shadow" v-if="selectedFetcher" :title="fetcherModules[selectedFetcher].name" size="lg">
                        <div class="table-responsive">
                            <div>
                                <div class="form-group">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon1">🔍</span>
                                        </div>
                                        <input type="text" v-model="filterFetcher" class="form-control" placeholder="{{ __('Search') }}" aria-label="search" aria-describedby="basic-addon1">
                                    </div>
                                </div>
                                <div v-if="fetcherModules[selectedFetcher].data.length > 0">
                                    <table class="table align-items-center">
                                        <thead class="thead-light">
                                            <tr>
                                                <th scope="col" class="sort" data-sort="name">{{ __('Items')}}</th>
                                                <th scope="col" class="sort" data-sort="name">
                                                    <div class="d-flex justify-content-end">
                                                        <b-button pill class="btn btn-default btn-sm" @click="refreshLinkData(selectedFetcher)" :disabled="isRefreshingLinks">
                                                            <span v-if="isRefreshingLinks">{{ __('Loading new data') }}</span>
                                                            <span v-else>{{ __('Reload data') }}</span>
                                                        </b-button>
                                                    </div>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="list">
                                            <tr v-for="item in filteredFetcherData" @click="sendLinkMessage(item.link)" class="cursor-pointer hover:bg-gray-50">
                                                <td class="p-3">
                                                    <div class="d-flex align-items-center">
                                                        <div class="mr-3">
                                                            <img :src="item.image" class="rounded shadow" style="width: 64px; height: 64px; object-fit: cover;">
                                                        </div>
                                                        <div style="width: 100%">
                                                            <h5 class="mb-1" style="word-wrap: break-word">@{{ item.title }}</h5>
                                                            <p class="text-muted mb-0 small">@{{ item.description }}</p>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </b-modal>

                  </div>


                
            </div>
        @endif
        <!-- Documents tab -->
        <div v-if="chatTab === 'documents'">
            <!-- Add content for Documents tab -->
            <div class="align-items-center">
                <div class="p-4 d-flex quick-replies-container border-radius-lg border">
                    <button type="button" class="btn w-100 btn-outline btn-outline-default shadow-none bg-default" id="img-btn" @click="openImageSelector">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#FFF" class="w-6 h-6" style="width: 20px; height:20px">
                            <path fill-rule="evenodd" d="M1.5 6a2.25 2.25 0 012.25-2.25h16.5A2.25 2.25 0 0122.5 6v12a2.25 2.25 0 01-2.25 2.25H3.75A2.25 2.25 0 011.5 18V6zM3 16.06V18c0 .414.336.75.75.75h16.5A.75.75 0 0021 18v-1.94l-2.69-2.689a1.5 1.5 0 00-2.12 0l-.88.879.97.97a.75.75 0 11-1.06 1.06l-5.16-5.159a1.5 1.5 0 00-2.12 0L3 16.061zm10.125-7.81a1.125 1.125 0 112.25 0 1.125 1.125 0 01-2.25 0z" clip-rule="evenodd" />
                        </svg>
                        <span style="color: #FFF">
                            {{__('Image')}}
                        </span>
                    </button>

                    <button type="button" class="btn w-100 btn-outline btn-outline-default shadow-none bg-default" id="file-btn" @click="openFileSelector">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#FFF" class="w-6 h-6" style="width: 20px; height:20px">>
                            <path d="M5.625 1.5c-1.036 0-1.875.84-1.875 1.875v17.25c0 1.035.84 1.875 1.875 1.875h12.75c1.035 0 1.875-.84 1.875-1.875V12.75A3.75 3.75 0 0016.5 9h-1.875a1.875 1.875 0 01-1.875-1.875V5.25A3.75 3.75 0 009 1.5H5.625z" />
                            <path d="M12.971 1.816A5.23 5.23 0 0114.25 5.25v1.875c0 .207.168.375.375.375H16.5a5.23 5.23 0 013.434 1.279 9.768 9.768 0 00-6.963-6.963z" />
                        </svg>
                        <span style="color: #FFF">
                            {{__('File')}}
                        </span>
                    </button>

                    <input accept="image/*, video/*, audio/*" @change="handleImageChange" type="file" ref="imageInput" style="display: none" />
                    <input accept=".pdf, .doc, .docx" @change="handleFileChange" type="file" ref="fileInput" style="display: none" />
                </div>
            </div>
        </div>



        <!-- Quick Replies -->
        <div v-if="chatTab === 'quick-replies'" class="p-4 d-flex quick-replies-container border-radius-lg border">

            <b-button v-if="!mobileChat" class="btn w-100 btn-outline btn-outline-default shadow-none bg-default" v-b-modal.modal-templates style="">
        
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#FFF" class="w-6 h-6" style="width: 20px; height:20px">
                  <path fill-rule="evenodd" d="M7.502 6h7.128A3.375 3.375 0 0118 9.375v9.375a3 3 0 003-3V6.108c0-1.505-1.125-2.811-2.664-2.94a48.972 48.972 0 00-.673-.05A3 3 0 0015 1.5h-1.5a3 3 0 00-2.663 1.618c-.225.015-.45.032-.673.05C8.662 3.295 7.554 4.542 7.502 6zM13.5 3A1.5 1.5 0 0012 4.5h4.5A1.5 1.5 0 0015 3h-1.5z" clip-rule="evenodd" />
                  <path fill-rule="evenodd" d="M3 9.375C3 8.339 3.84 7.5 4.875 7.5h9.75c1.036 0 1.875.84 1.875 1.875v11.25c0 1.035-.84 1.875-1.875 1.875h-9.75A1.875 1.875 0 013 20.625V9.375zM6 12a.75.75 0 01.75-.75h.008a.75.75 0 01.75.75v.008a.75.75 0 01-.75.75H6.75a.75.75 0 01-.75-.75V12zm2.25 0a.75.75 0 01.75-.75h3.75a.75.75 0 010 1.5H9a.75.75 0 01-.75-.75zM6 15a.75.75 0 01.75-.75h.008a.75.75 0 01.75.75v.008a.75.75 0 01-.75.75H6.75a.75.75 0 01-.75-.75V15zm2.25 0a.75.75 0 01.75-.75h3.75a.75.75 0 010 1.5H9a.75.75 0 01-.75-.75zM6 18a.75.75 0 01.75-.75h.008a.75.75 0 01.75.75v.008a.75.75 0 01-.75.75H6.75a.75.75 0 01-.75-.75V18zm2.25 0a.75.75 0 01.75-.75h3.75a.75.75 0 010 1.5H9a.75.75 0 01-.75-.75z" clip-rule="evenodd" />
                </svg>
                <span style="color: #FFF">
                    {{__('Template')}}
                </span>
                
                
          </b-button>

          <!-- Link fetcher modal -->
        

         

      
          <b-modal id="modal-templates" scrollable hide-backdrop content-class="shadow" title="{{__('Send template message')}}">
              <div class="table-responsive">
                  <div>
                      <div class="form-group">
                          <div class="input-group">
                              <div class="input-group-prepend">
                                  <span class="input-group-text" id="basic-addon1">🔍</span>
                              </div>
                              <input type="text" v-model="filterTemplates" class="form-control" placeholder="{{ __('Search') }}" aria-label="seeach" aria-describedby="basic-addon1">
                          </div>
                      </div>
                      <table class="table align-items-center">
                          <thead class="thead-light">
                              <tr>
                                  <th scope="col" class="sort" data-sort="name">{{ __('Template')}}</th>
                                  
                              </tr>
                          </thead>
                          <tbody class="list">
                              <tr  v-for="(template) in filteredTemplates">
                                  <td class="">
                                      <a :href="'/campaigns/create?template_id='+template.id+'&send_now=on&contact_id='+activeChat.id" ><span class="name mb-0 text-sm">@{{ template.name }}</span></a>
                                  </td>
                              </tr>
                          </tbody>
                      </table>
                  </div>
              </div>
      
          </b-modal>

          
      
          <b-button class="btn w-100 btn-outline btn-outline-default shadow-none bg-default" type="button" v-b-modal.modal-replies>
              <span class="btn-inner--icon"><i class="ni ni-curved-next" style="color:#FFF "></i></span>
              <span style="color: #FFF">
                {{__('Quick replies')}}
            </span>
          </b-button>
      
          <b-modal id="modal-replies" scrollable hide-backdrop content-class="shadow" title="{{__('Quick replies')}}">
              <div class="table-responsive">
                  <div>
                      <div class="form-group">
                          <div class="input-group">
                              <div class="input-group-prepend">
                                  <span class="input-group-text" id="basic-addon1">🔍</span>
                              </div>
                              <input type="text" v-model="filterText" class="form-control" placeholder="{{ __('Search') }}" aria-label="seeach" aria-describedby="basic-addon1">
                          </div>
                      </div>
                      <table class="table align-items-center">
                          <thead class="thead-light">
                              <tr>
                                  <th scope="col" class="sort" data-sort="name">{{ __('Reply')}}</th>
                                  <th scope="col" class="sort" data-sort="name">
                                      <div class="d-flex justify-content-end">
                                          <b-button pill class="btn btn-default btn-sm" href="{{ route('replies.index',['type'=>'qr'])}}">
                                              <b>{{ __('Manage Quick replies') }}</b>
                                          </b-button>
                                      </div>
                                  </th>
                              </tr>
                          </thead>
                          <tbody class="list">
                              <tr v-for="(reply, index) in filteredReplies">
                                  <td colspan="2" class="">
                                      <span @click="setMessage(reply.text)" class="name mb-0 text-sm">@{{ reply.name }}</span>
                                  </td>
                              </tr>
                          </tbody>
                      </table>
                  </div>
              </div>
      
          </b-modal>
      
        </div>
        
    </div>
</div>