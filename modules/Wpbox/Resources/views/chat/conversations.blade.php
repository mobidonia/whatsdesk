<div class="contact-list card border border-right-0 border-radius-lg mb-5 mb-lg-0 overflow-auto overflow-x-hidden h-100">
    <div class="card-header border-bottom p-4">
        <div class="d-flex flex-column">
            <h2 class="mb-0">{{__('Chats')}}&nbsp;&nbsp;<b-badge pill variant="primary">@{{totalMessagesCount}}</b-badge></h2>
            <div class="search-box w-100 mt-3">
                <b-input-group size="md" class="shadow-sm hover-shadow-lg transition-all duration-200">
                    <b-form-input
                        v-model="searchQuery"
                        placeholder="{{__('Search chats...')}}"
                        class="border-radius-lg border-0 px-4 py-3 text-sm font-medium"
                        style="background: linear-gradient(to right, #f8f9fa, #ffffff);"
                    ></b-form-input>
                    <b-input-group-append>
                        <div class="border-radius-lg border-0 px-4 d-flex align-items-center" style="background: linear-gradient(to right, #f8f9fa, #ffffff);">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                            </svg>
                        </div>
                    </b-input-group-append>
                </b-input-group>
            </div>
        </div>
    </div>

    <!--  @click.prevent="newTab" -->   
    <div class="w-100 px-3 pt-4" style="">

        <div>
            <b-tabs pills small link justified >
              
              <b-tab @click.prevent="allMessages" title-link-class="small-tab"  title-link-style="padding: 0.35rem 0.5rem;">
                <template #title >
                    {{__('Open')}}                     
                   </template>
              </b-tab>
              <b-tab title-link-class="small-tab" @click.prevent="mineMessages">
                <template #title >
                    {{__('Mine')}} <b-badge variant="primary">@{{myMessagesCount}}</b-badge>
                  </template>
               </b-tab>
              <b-tab @click.prevent="newMessages" title-link-class="small-tab">
                <template #title >
                    {{__('New')}} <b-badge variant="primary">@{{newMessagesCount}}</b-badge>
                   </template>
              </b-tab>
              <b-tab @click.prevent="resolvedMessages" title-link-class="small-tab">
                <template #title >
                    {{__('Closed')}}
                   </template>
              </b-tab>
            </b-tabs>
        </div>
    </div>
    <div class="card-body px-3 d-flex flex-column h-100"   v-cloak >


        <div  v-for="contact in contacts" :class="[ 'd-block','p-3',' border-radius-lg', 'mw-100' ,{ 'contact-selected': contact.isActive }]"  v-cloak>
            <div v-cloak class="d-flex" v-on:click="setCurrentChat(contact.id)">

               
                <div class="d-flex w-100" style="gap:1rem;">
                    <div v-if="contact.name&&contact.name[0]&&(contact.avatar==''||contact.avatar==null)" class="avatar avatar-content bg-gradient-success" style="min-width:48px; height:48px; display:flex; align-items:center; justify-content:center;">@{{contact.name[0]}}</div>
                    <img v-if="contact.avatar!='' && contact.avatar!=null" alt="Image" :src="contact.avatar" :data-src="contact.avatar" class="avatar">
                    <div class="d-flex flex-column w-100" >
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 :class="[ 'mb-0',  {'text-primary': contact.isActive} ]">@{{ contact.name }}</h4>
                            <span :class="['text-nowrap','text-xs','text-muted', 'opacity-6', 'px-2' ]">@{{ momentIt(contact.last_reply_at) }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <p :style="{ fontWeight: contact.is_last_message_by_contact === 1 ? '700' : 'normal' }" :class="[ 'mb-0','text-sm', 'text-muted']">@{{ contact.last_message }}</p>
                            <span class="px-3" :style="{ display: contact.is_last_message_by_contact === 1 ? 'inline-flex' : 'none' }">
                                <svg width="14" class="fill-primary" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><path d="M256 512v0c141.38 0 256-114.62 256-256C512 114.61 397.38 0 256 0v0C114.61 0 0 114.61 0 256c-.001 141.38 114.61 256 256 256Z"/></svg>
                            </span>
                        </div>
                    </div>
                </div>


            </div>
            
        </div>
        
        <div class="d-flex justify-content-center mt-3" v-if="numberOfPages>1">
            <b-pagination hide-goto-end-buttons v-model="page" :total-rows="numberOfPages" :per-page="1"  @change="allMessages"></b-pagination>
        </div>

        <div class="d-flex justify-content-between align-items-center h-100 empty-chats" v-if="contacts.length === 0"></div>



    </div>
</div>