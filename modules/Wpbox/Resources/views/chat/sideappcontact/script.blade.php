<script>
    "use strict";

    var updateContact=function(){
        console.log("updateContact");

        //Get all the contact details
        var contactDetails=chatList.activeChat;

        //Get the new contact details
        var newContactDetails={
            name: contactDetails.name,
            email: contactDetails.email,
            id: contactDetails.id
        };

       //Make a request to update the contact
       axios.post('/api/wpbox/updateContact',newContactDetails)
        .then(response=>{
            console.log(response);
       })
    }

    var updateAIBotStatus = function() {
        var contactId = chatList.activeChat.id;
        var enabled = chatList.activeChat.enabled_ai_bot;

        axios.post('/api/wpbox/updateAIBot', {
            id: contactId,
            enabled_ai_bot: enabled ? '1' : '0',
            token: '_'
        })
        .then(response => {
            console.log('AI Bot status updated');
        })
        .catch(error => {
            console.error('Error updating AI bot status:', error);
            // Revert the toggle if the update fails
            chatList.activeChat.enabled_ai_bot = !chatList.activeChat.enabled_ai_bot;
        });
    }

    //Add this function to the Vue instance of chatList
    window.addEventListener('load', function() {
        //Watch for changes in activeChat
        chatList.$watch('activeChat', function(newVal, oldVal) {
            if(newVal !== oldVal) {
               //Get the new contact details
               var newContactDetails={
                name: newVal.name,
                email: newVal.email,
                id: newVal.id
               }

               //Get the custom fields from the new contact, and add them to the newContactDetails object
               //Get them from API
            }
        });

        // Add updateAIBotStatus to chatList methods
        chatList.updateAIBotStatus = updateAIBotStatus;
    });
</script>
