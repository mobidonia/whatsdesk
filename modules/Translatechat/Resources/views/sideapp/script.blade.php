<script>
    "use strict";

    //Add this function to the Vue instance of chatList
    window.addEventListener('load', function() {

        //Add data properties
        chatList.$set(chatList.$data, 'originalMessage', '');
        chatList.$set(chatList.$data, 'convertedMessage', '');
        chatList.$set(chatList.$data, 'summary', '');

        chatList.$set(chatList.$data, 'aiQuestion', '');
        chatList.$set(chatList.$data, 'aiResponse', '');
        chatList.$set(chatList.$data, 'aiError', '');

        //Add dynamic properties

        chatList.addProperty('aiResponse', '');
        chatList.addProperty('aiPrompt', '');   
        chatList.addProperty('aiSummary', '');
        chatList.addProperty('isAskingAI', false);
        chatList.addProperty('isGeneratingSummary', false);
       


       

        //askAI
        chatList.askAI = function() {
            var question = chatList.aiQuestion;
            console.log('Asking AI for question:', question);

            chatList.updateProperty('isAskingAI', true);
 

            //Call API to ask AI
            axios.post('/api/translate/ask-ai', {
                message: question,
                chat_id: chatList.activeChat.id
            }).then(response => {
                var answer = response.data.answer;
                console.log('AI response:', answer);
                chatList.updateProperty('aiResponse', answer);
         
                chatList.updateProperty('isAskingAI', false);
               
              

            }).catch(error => {
                console.error('Error asking AI:', error);
            });
        }

        chatList.copyAIResponseToClipboard = function() {
            navigator.clipboard.writeText(chatList.dynamicProperties.aiResponse);
        }

        chatList.summarizeChat = function() {
            console.log('Summarizing chat for ID:', chatList.activeChat.id);
            chatList.activeChat.summary = "Waiting for summary...";
            chatList.updateProperty('isGeneratingSummary', true);
           
            axios.get('/api/translate/summarize-chat/'+chatList.activeChat.id).then(response => {
                console.log('Summarizing chat for ID INSIDE:', chatList.activeChat.name);
                chatList.activeChat.summary = "Waiting for summary...";
                chatList.activeChat.name = "Test";
                if(response.data.success) {
                    var summary = response.data.summary;
                    console.log('Chat summary:', summary);
                    chatList.activeChat.summary = summary;
                    
                    
                }
                chatList.updateProperty('isGeneratingSummary', false);
            }).catch(error => {
                console.error('Error summarizing chat:', error);
                chatList.activeChat.summaryError = error.response.data.message;
                chatList.updateProperty('isGeneratingSummary', false);
            });
        };

        //Add methods
        chatList.convertStyle = function(style) {
            if(!this.originalMessage) {
                return;
            }

            console.log(style);
            console.log(this.originalMessage);
            
            //Call API to convert style
            axios.post('/api/translate/convert-style', {
                message: this.originalMessage,
                style: style
            }).then(response => {
                if(response.data.success) {
                    var convertedMessage = response.data.message;
                    console.log(convertedMessage);

                    //Set in the chatList
                    this.activeMessage = convertedMessage;
                }
            }).catch(error => {
                console.error('Error converting style:', error);
            });
        };
        

        

        

        

        //Watch for changes in activeChat
        chatList.$watch('activeChat', function(newVal, oldVal) {
            if(newVal !== oldVal) {
                chatList.activeChat.isAskingAI = false;
            }
        });




    });
</script>
