<script>
    "use strict";
    var chatList=null;
    var lastmessagetime="none";
    var chatMessages={};
    var pusherConn = null;
    var pusherConnForUpdates = null;
    var channel = null;
    var channelUpdate=null;
    var pusherActiveChat=null;
    var companyID="<?php echo auth()->user()->getCurrentCompany()->id; ?>";
    var serverTimezone = "<?php echo config('app.timezone'); ?>";
    var pusherAvailable=false;
    var searchQuery="";
   
    
    

    var initPusher=function(){
        if (typeof Pusher !== 'undefined') {
            // The variable is defined
            // You can safely use it here
            Pusher.logToConsole = false;

            pusherConn = new Pusher(PUSHER_APP_KEY, {
                cluster: PUSHER_APP_CLUSTER
            });
            pusherAvailable=true;

            pusherConnForUpdates = new Pusher(PUSHER_APP_KEY, {
                cluster: PUSHER_APP_CLUSTER
            });

            //Bind to new chat list update
            channelUpdate = pusherConnForUpdates.subscribe('chatupdate.'+companyID);
            channelUpdate.bind('general', chatListUpdate);

            // Also subscribe for WhatsApp incoming calls to enable instant UIC handling
            try{
                const wcChan = pusherConnForUpdates.subscribe('whatsappcall.'+companyID);
                wcChan.bind('incoming', function(call){
                    try{
                        if(window.wpIncomingCall){ window.wpIncomingCall(call); }
                    }catch(err){ console.error('UIC handler error', err); }
                });
                wcChan.bind('ended', function(call){
                    try{
                        if(window.wpCallEnded){ window.wpCallEnded(call); }
                    }catch(err){ console.error('UIC ended handler error', err); }
                });
                wcChan.bind('claimed', function(call){
                    try{
                        if(window.wpCallClaimed){ window.wpCallClaimed(call); }
                    }catch(err){ console.error('UIC claimed handler error', err); }
                });
            }catch(err){ console.warn('Failed to subscribe to whatsappcall channel', err); }

            

        } else {
            // Pusher
            js.notify("Error: Pusher is not defined. Chat will not load new messages. Please check documentation","danger");
        }
    }


    var connectToChannel=function(chatID){
        if(pusherActiveChat!=chatID && pusherAvailable){
            if(channel!=null){
                //Change chat, release old one
                channel.unsubscribe();
                channel.unbind('general', receivedMessageInPusher);
            }
            //Set active chat
            pusherActiveChat=chatID;

            //Bind to new chat
            channel = pusherConn.subscribe('chat.'+chatID);
            channel.bind('general', receivedMessageInPusher);

            

        }else{
            //Same chat, no changes
        }
    }

    var receivedMessageInPusher=function(data){
        
        const index = chatList.contacts.findIndex(item => item.id === data.contact.id);
        chatMessages[data.contact.id].push(data.message);
      
        //Update the last message
        chatList.contacts[index].last_message = data.message.value;

        //Scroll to bottom
        setTimeout(() => {
            if($('#chatMessages')[0]&&$('#chatMessages')[0].scrollHeight){
                $('#chatMessages').scrollTop($('#chatMessages')[0].scrollHeight); 
            }
            
        }, 1000);
        
        
        
    }

    var chatListUpdate=function(data){
        
        
        if(data.contact!==chatList.activeChat.id){
            
            getChatsJS();
        }else{
            //Same chat
            
        }
    }

    


    

    var getChatJS=function(contact_id){
        if(chatMessages[contact_id]){
            //Previous messages
            chatList.messages=chatMessages[contact_id];
        }
        axios.get('/api/wpbox/chat/'+contact_id).then(function (response) {
            var messages=response.data.data;
            messages=messages.reverse();
            chatMessages[contact_id]=messages;
           
            chatList.messages=chatMessages[contact_id];

            //Loot in  chatList.contacts=response.data.data;
            const index = chatList.contacts.findIndex(item => item.id === contact_id);
            if (index !== -1) {
                chatList.contacts[index].is_last_message_by_contact=0;
            }
            
        }).catch(function (error) {
            
        });

        connectToChannel(contact_id);
        
        
    }

    var getChatsJS=function(page=1,search_query=""){
        console.log("Search query");
        console.log(search_query);
        axios.get('/api/wpbox/chats/'+lastmessagetime+'/'+page+'/'+search_query).then(function (response) {
            if(response.data.status){
                var initialChatLoad=chatList.contacts.length==0;
                chatList.contacts=response.data.data.filter(contact => !contact.resolved_chat);
                chatList.all=response.data.data;
                chatList.numberOfPages=response.data.numberOfPages;

                chatList.myMessagesCount=response.data.myChatsCount;
                chatList.totalMessagesCount=response.data.totalChats;
                chatList.newMessagesCount=response.data.newMessagesCount;


                if(chatList.contacts.length>0){
                    
                    if(chatList.activeChat.id==null){
                        /*getChatJS(chatList.contacts[0].id);
                        chatList.contacts[0].isActive=true;
                        chatList.activeChat=chatList.contacts[0];*/
                    }else{
                        //Stays the same last active chat
                        const index = chatList.contacts.findIndex(item => item.id === chatList.activeChat.id);
                        if (index !== -1) {
                            chatList.contacts[index].name = chatList.contacts[index].name+" ";
                            chatList.contacts[index].isActive = true;
                        }
                    }
                    lastmessagetime=chatList.contacts[0].last_reply_at; 
                    
                    //Play Sound
                    if(!initialChatLoad){
                        playSound();
                    }
                    

                }
            }
            
        }).catch(function (error) {
            
        });
    }

    function playSound() {
        if(!chatList.stopPlaySound){
            var audio = new Audio('/vendor/meta/pling.mp3');
            audio.play();
        }
        chatList.stopPlaySound=false;
    }

    function escapeSingleQuotesInJSON(jsonString) {
        // Use a regular expression to find and replace single quotes inside string values
        const escapedJSONString = jsonString.replace(/"([^"]*?)":\s*"([^"]*?)"/g, function(match, key, value) {
            const escapedValue = value.replace(/'/g, "\\'");
            return `"${key}": "${escapedValue}"`;
        });

        return escapedJSONString;
    }

    

    window.onload = function () {
        initPusher();
        getChatsJS();
        axios.defaults.baseURL = window.location.origin;

        //Emoji picker
        setTimeout(() => {
            new EmojiPicker({
                trigger: [
                    {
                    selector: '#emoji-btn',
                    insertInto: '#message'

                }
            ],
           
            closeButton: true,
            specialButtons: 'green' // #008000, rgba(0, 128, 0);
        });
        }, 1000);
        //VUE Chat list
        Vue.config.devtools=true;

        
        chatList = new Vue({
        el: '#chatList',
        data: {
            templates: @json($templates),
            replies: @json($replies),
            users: @json($users),
            languages: @json($languages),
            currentUserID: "{{auth()->user()->id}}",
            contacts: [],
            all:[],
            stopPlaySound:false,
            numberOfPages:1,
            page:1,
            activeChat:{},
            activeChatGroups:{},
            activeChatCustomFields:{},
            messages:[],
            activeMessage:"",
            activeNote:"",
            selectedImage: null,
            selectedFile: null,
            filterText: '',
            filterTemplates: '',
            mobileChat:window.innerWidth<768,
            conversationsShown:true,
            tab:"all",
            chatTab:"reply",
            fetcherModules: @json($fetcherModules),
            selectedFetcher:null,
            filterFetcher:"",
            isRefreshingLinks:false,
            currentSideApp: null,
            currentSideAppName: null,
            searchQuery:"",
            newMessagesCount: 0,
            myMessagesCount: 0,
            totalMessagesCount: 0,
            dynamicProperties: {}, // Placeholder object
        },
        errorCaptured(err, component, info) {
            console.error('An error occurred:', err);
            console.error('Component in which error occurred:', component);
            console.error('Additional information:', info);
            return false; // this ensures that we still get the default behavior
        },
       computed: {
            filteredReplies() {
                const filterText = this.filterText.toLowerCase();
                return this.replies.filter(item => item.name.toLowerCase().includes(filterText));
            },
            filteredTemplates() {
                const filterTemplates = this.filterTemplates.toLowerCase();
                return this.templates.filter(item => item.name.toLowerCase().includes(filterTemplates));
            },
            filteredFetcherData(){
                const filterFetcher = this.filterFetcher.toLowerCase();
                return this.fetcherModules[this.selectedFetcher].data.filter(item => item.title.toLowerCase().includes(filterFetcher));
            }
        },
        watch: {
            page(newVal, oldVal) {
                if (newVal !== oldVal) {
                    this.stopPlaySound=true;
                    //Clear search query
                    this.searchQuery="";
                    getChatsJS(newVal);
                }
            },
            searchQuery(newVal, oldVal) {
                if (newVal !== oldVal) {
                    this.stopPlaySound=true;
                    getChatsJS(this.page, newVal);
                }
            }
        },
        methods: {
            async requestWaCallPermission(){
            },
            marked(text) {
                if (!text) return '';
            
                return text
                        // Headers
                        .replace(/^### (.*$)/gm, '<h3>$1</h3>')
                        .replace(/^## (.*$)/gm, '<h2>$1</h2>')
                        .replace(/^# (.*$)/gm, '<h1>$1</h1>')
                        // Bold
                        .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                        // Italic
                        .replace(/\*(.*?)\*/g, '<em>$1</em>')
                        // Links
                        .replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2">$1</a>')
                        // Lists
                        .replace(/^\s*\-\s(.*)/gm, '<li>$1</li>')
                        // Line breaks
                        .replace(/\n/g, '<br>');
            },
            addProperty() {
                // Dynamically add a property using $set
                this.$set(this.dynamicProperties, 'newProperty', 'value');
            },
            updateProperty(property, value) {
                this.$set(this.dynamicProperties, property, value);
            },
            getProperty(property) {
                return this.dynamicProperties[property];
            },
            switchChatTab(tab){
                this.chatTab=tab;
            },
            mineMessages:function(){
                this.tab="mine";
                this.filterContacts();
            },
            allMessages:function(){
                this.tab="all";
                this.filterContacts();
            },
            newMessages:function(){
                this.tab="new";
                this.filterContacts();
            },
            resolvedMessages:function(){
                this.tab="resolved";
                this.filterContacts();
            },
            filterContacts() {
                const index = this.contacts.findIndex(item => item.id === chatList.activeChat.id);
                        if (index !== -1) {
                            chatList.contacts[index].name = chatList.contacts[index].name+" ";
                            chatList.contacts[index].isActive = true;
                        }

                if(this.tab=="all"){
                    this.contacts=this.all.filter(contact => !contact.resolved_chat);
                }else if(this.tab=="mine"){
                    this.contacts=this.all.filter(contact => contact.user_id==this.currentUserID);
                }else if(this.tab=="new"){
                    this.contacts=this.all.filter(contact => contact.is_last_message_by_contact);
                }else if(this.tab=="resolved"){
                    this.contacts=this.all.filter(contact => contact.resolved_chat);
                }
            },
            formatIt: function(message){
                
                const linkRegex = /https?:\/\/[^\s/$.?#].[^\s]*/g;

                // Replace links with placeholders for rendering
                var replacedText = message.replace(linkRegex, '<a href="$&" class="text-bold">$&</a>');

                //Replace \n with <br>
                replacedText = replacedText.replace(/\n/g, '<br>');

                return replacedText;
            
            },
            getAssignedUser: function(contact){
                if(contact.user_id){
                    const user = Object.keys(this.users).find(user => user == contact.user_id);
                    return this.users[user] ? this.users[user] : '-';
                }
                return 'Not assigned';
            },
            translationLanguage(contact){
                return contact.language &&contact.language!="none"  ? contact.language : "{{ __('No translation')}}";
            },
            setLanguage: function(lang, contact){
                axios.post('/api/wpbox/setlanguage/'+contact.id, {language: lang}).then(function (response) {
                    if(response.data.status){
                        contact.language=lang;
                    }else{
                        js.notify(response.data.errMsg,"danger");
                    }
                }).catch(function (error) {
                    console.log(error);
                });
            },
            assignUser: function(user_id, contact_id){
                axios.post('/api/wpbox/assign/'+contact_id, {user_id: user_id}).then(function (response) {
                    if(response.data.status){
                        chatList.activeChat.user_id=user_id;
                        const indexUpdate = chatList.all.findIndex(item => item.id == contact_id);
                        console.log(indexUpdate);
                        if (indexUpdate !== -1) {
                            chatList.all[indexUpdate].user_id = user_id;
                        }
                        chatList.filterContacts();
                    }else{  
                        js.notify(response.data.errMsg,"danger");
                    }
                }).catch(function (error) {
                    console.log(error);
                });
            },
            getReplyNotification(contact){
                var timeSinceLastClientReply= moment.tz(contact.last_client_reply_at,serverTimezone).add(24, 'hours');
                const minutesDifference = timeSinceLastClientReply.diff(moment.now(), 'minutes');
                var statusOfReply={
                    "class":"badge-danger",
                    "text":"{{ __('You can reply only with template')}}!"
                };
                if(minutesDifference>0){
                    if(minutesDifference>60){
                        statusOfReply.class="badge-success";
                        statusOfReply.text=moment.duration(minutesDifference, 'minutes').humanize();
                    }else{
                        statusOfReply.class="badge-warning";
                        statusOfReply.text=moment.duration(minutesDifference, 'minutes').humanize();
                    }
                    statusOfReply.text+=" {{ __('left to reply')}}";
                }
                return statusOfReply;
            },
            setCurrentChat: function (contact_id) {


                if(this.mobileChat){
                    this.conversationsShown=false;
                }
                
                getChatJS(contact_id);

                console.log("Remove previous active chat");
                const indexRemove = this.all.findIndex(item => item.id === this.activeChat.id);
                console.log(indexRemove);
                if (indexRemove !== -1 && this.all[indexRemove]) {
                    // Make sure the object exists before modifying it
                    if (this.all[indexRemove].name) {
                        this.all[indexRemove].name = this.all[indexRemove].name + " ";
                    }
                    this.all[indexRemove].isActive = false;
                }
                
                console.log("Set new active chat");
                const index = this.all.findIndex(item => item.id === contact_id);
                console.log(index);
                if (index !== -1 && this.all[index]) {
                    console.log("Set new active chat for index "+index);
                    console.log(this.all[index]);
                    this.all[index].name = this.all[index].name+" ";
                    this.all[index].isActive = true;
                    this.activeChat = this.all[index];
                    console.log("Active chat set to "+index);
                    console.log(this.all[index].name);

                    // Fetch contact's groups
                    console.log("Fetch contact's groups");
                    axios.get('/api/wpbox/contact-groups-and-custom-fields/' + contact_id)
                        .then(response => {
                            this.activeChatGroups = response.data.groups;
                            this.activeChatCustomFields = response.data.customFields;
                        })
                        .catch(error => {
                            console.error('Error fetching contact groups:', error);
                            this.activeChatGroups = {};
                            this.activeChatCustomFields = {};
                        });
                }

                setTimeout(() => {
                    this.scrollToBottomOfChat();
                }, 1000);
               
               




                
            },
            getChats:function (){
                getChatsJS(this.page,this.searchQuery);
            },
            momentIt: function (date) {
                return moment.tz(date,serverTimezone).fromNow();
            },
            momentHM: function (date) {
                return moment.tz(date,serverTimezone).format('HH:mm');;
            },
            momentDay:function (date) {
                return moment.tz(date,serverTimezone).format('dddd, D MMM, YYYY');
            },
            momentDaySimple:function (date) {
                return moment.tz(date,serverTimezone).format('D MMM, YYYY');
            },
            scrollToBottomOfChat() {
                const scrollableDiv = this.$refs.scrollableDiv;
                if( scrollableDiv && scrollableDiv.scrollHeight){
                    scrollableDiv.scrollTop = scrollableDiv.scrollHeight;
                   
                }
            },
            parseJSON:function(jsonString){
                if(jsonString==null||jsonString==""){
                    return [];
                }
                return JSON.parse(jsonString);
            },
            setMessage(message){
                this.$bvModal.hide('modal-replies');    
                message=message.replace("\{\{name\}\}",this.activeChat.name);   
                message=message.replace("\{\{phone\}\}",this.activeChat.phone);   
                this.activeMessage=message;
            },
            setVueMessage(message){
                this.activeMessage=this.activeMessage+message;
            },
            sendLinkMessage(link){
                console.log(link);
                this.activeMessage=link;

                //Close the modal
                this.$bvModal.hide('modal-link-fetcher');

                //On the next tick
                this.$nextTick(() => {
                    this.sendChatMessage();

                    //Clear the filter
                    this.filterFetcher="";
                });
            },
            toggleSideApp(appName,appTitle) {
                if (this.currentSideApp === appName) {
                    this.closeSideApp();
                } else {
                    this.currentSideApp = appName;
                    this.currentSideAppName = appTitle;
                    // this.chatAndToolsContentClass="col-8";
                    // this.sideAppsClass="col-4";

                    //Add transition class for smooth width change
                    document.querySelector('#chatAndTools').classList.add('transition');
                    document.querySelector('#sideApps').classList.add('transition');
                    document.querySelector('#sideBarButtons').classList.add('rounded-0');
                    //document.querySelector('#dropdown-right__BV_button_').classList.add('d-none');

                    //Use setTimeout to ensure transition class is applied before changing columns
                    setTimeout(() => {
                    document.querySelector('#chatAndTools').classList.remove('transition');
                    document.querySelector('#sideApps').classList.remove('transition');
                        // document.querySelector('#chatAndTools').classList.remove('col-11');
                        // document.querySelector('#chatAndTools').classList.add('col-8');
                        // document.querySelector('#sideApps').classList.remove('col-1'); 
                        // document.querySelector('#sideApps').classList.add('col-4');
                    }, 50);
                }
            },
            closeSideApp() {
                this.currentSideApp = null;
                document.querySelector('#dropdown-right__BV_button_').classList.remove('d-none');
                document.querySelector('#sideBarButtons').classList.remove('rounded-0');
                // document.querySelector('#chatAndTools').classList.remove('col-8');
                // document.querySelector('#chatAndTools').classList.add('col-11');
                // document.querySelector('#sideApps').classList.remove('col-4');
                // document.querySelector('#sideApps').classList.add('col-1');
            },
            capitalize(value) {
                if (!value) return '';
                value = value.toString();
                return value.charAt(0).toUpperCase() + value.slice(1);
            },
            refreshLinkData(alias){
                console.log(alias+" --> Load new data");
                this.isRefreshingLinks=true;
                //Reload the data, by making a AJAX call to /alias/getData/1
                axios.get('/'+alias+'/getData/1').then(function (response) {
                    //Set the data to the fetcherModules[alias].data
                    chatList.fetcherModules[alias].data=response.data;
                    chatList.isRefreshingLinks=false;
                });
            },
            sendChatMessage(){
               
            
                var message=this.activeMessage;
                this.activeMessage="";
                axios.post('/api/wpbox/send/'+chatList.activeChat.id, {message: message}).then(function (response) {
                    
                    if(response.data.status){
                        lastmessagetime=response.data.messagetime;

                    }else{
                        js.notify(response.data.errMsg,"danger");
                    }}).catch(function (error) {
                
                    });
                    
            },
            sendNote(){
                var note=this.activeNote;
                this.activeNote = "";
                axios.post('/api/wpbox/sendnote/'+chatList.activeChat.id, {note: note}).then(function (response) {
                    if(response.data.status){
                        
                        this.$nextTick(() => {
                            this.$refs.noteTextarea.value = "";
                        });
                    }else{
                        js.notify(response.data.errMsg,"danger");
                    }
                }).catch(function (error) {
                    console.log(error);
                });
            },
            showConversations(){
                const indexRemove = this.contacts.findIndex(item => item.id === this.activeChat.id);
                if (indexRemove !== -1) {
                    this.contacts[indexRemove].name = this.contacts[indexRemove].name+" ";
                    this.contacts[indexRemove].isActive = false;
                }   
                this.activeChat={};
                this.conversationsShown=true;
            },
            openImageSelector() {
                // Trigger the file input click event
                this.$refs.imageInput.click();
            },
            openFileSelector() {
                // Trigger the file input click event
                this.$refs.fileInput.click();
            },
            handleImageChange(event) {
                // Get the selected image file
                this.selectedImage = event.target.files[0];

                if (!this.selectedImage) {
                    alert('Please select an image first.');
                    return;
                }else{
                     // Create a FormData object to send the image to the API
                    const formData = new FormData();
                    formData.append('image', this.selectedImage);
                    axios.post('/api/wpbox/sendimage/'+chatList.activeChat.id, formData);
                }
            },
            handleFileChange(event) {
                // Get the selected file
                this.selectedFile = event.target.files[0];

                if (!this.selectedFile) {
                    alert('Please select a file first.');
                    return;
                }else{
                     // Create a FormData object to send the image to the API
                    const formData = new FormData();
                    formData.append('file', this.selectedFile);
                    axios.post('/api/wpbox/sendfile/'+chatList.activeChat.id, formData);
                }
            },
            openLinkFetcher(alias){
                this.selectedFetcher=alias;

                //On next tick
                this.$nextTick(() => {
                    //Open modal
                    this.$bvModal.show('modal-link-fetcher');
                });

            },
            updateAIBotStatus() {
                axios.post('/api/wpbox/updateAIBot', {
                    id: this.activeChat.id,
                    enabled_ai_bot: this.activeChat.enabled_ai_bot ? 1 : 0
                }).then(response => {
                    if (response.data.status === 'success') {
                        js.notify(response.data.message, 'success');
                    } else {
                        js.notify(response.data.message, 'danger');
                    }
                }).catch(error => {
                    console.error('Error updating AI Bot status:', error);
                    js.notify('Error updating AI Bot status', 'danger');
                });
            },

            updateChatStatus(contact_id) {
                axios.post('/api/wpbox/updateChatStatus/' + contact_id)
                    .then(response => {
                        if (response.data.status) {
                            this.activeChat.resolved_chat = 1;
                            js.notify('Chat marked as closed', 'success');
                            this.filterContacts();
                        } else {
                            js.notify(response.data.message, 'danger');
                        }
                    })
                    .catch(error => {
                        console.error('Error updating chat status:', error);
                        js.notify('Error updating chat status', 'danger');
                    });
            },
            reopenChat(contact_id) {
                axios.post('/api/wpbox/reopenChat/' + contact_id)
                    .then(response => {
                        if (response.data.status) {
                            this.activeChat.resolved_chat = 0;
                            js.notify('Chat reopened successfully', 'success');
                            this.filterContacts();
                        } else {
                            js.notify(response.data.message, 'danger');
                        }
                    })
                    .catch(error => {
                        console.error('Error reopening chat:', error);
                        js.notify('Error reopening chat', 'danger');
                    });
            },
        },
    })

     
};


</script>

<script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script>