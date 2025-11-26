<script>
    "use strict";

    //Add this function to the Vue instance of chatList
    window.addEventListener('load', function() {

        //Watch for changes in activeChat
        chatList.$watch('activeChat', function(newVal, oldVal) {
            if(newVal !== oldVal) {

                //Get all the notes of the contact
                axios.get('/api/wpbox/notes/' + newVal.id)
                .then(response => {
                    this.activeChatNotes = response.data.data;
                    console.log(this.activeChatNotes);
                })
                .catch(error => {
                    console.error('Error fetching notes:', error);
                });
            
            }
        });

    });
</script>
