

<!-- Display the name label -->
<div class="scrollable-content" style="height: calc(100vh - 100px); overflow-y: auto; padding: 10px;">

    <div class="d-flex flex-column w-100 mb-4">
        <div class="form-group mb-2 position-relative w-100">
            <textarea 
                @keyup.enter="sendChatMessage" 
                v-model='activeNote' 
                class="form-control shadow-sm border-0 w-100" 
                style="border-radius: 1rem; resize: none; padding: 1rem;"
                rows="2" 
                placeholder="{{ __('Type your note here...') }}"
            ></textarea>
        </div>
        <b-button 
            @click="sendNote" 
            class="btn btn-primary shadow-sm px-4 py-2 d-flex align-items-center align-self-end" 
            style="border-radius: 0.8rem; transition: all 0.2s;"
            type="button"
        >
            <i class="fas fa-paper-plane mr-2"></i>
            {{ __('Add Note') }}
        </b-button>
    </div>
    <hr />
    <div v-if="activeChatNotes && activeChatNotes.length > 0">
        <div v-for="note in activeChatNotes" :key="note.id">
            <div class="form-group text-center">
                <div class="card bg-primary" style="max-width: 100%; border-top-right-radius: 0rem; border-bottom-right-radius: 0rem;">
                    <div class="card-body py-2 px-3" style="border-radius: 0;">
                        <p class="mb-2 text-left text-white" style="text-align: left !important;" v-html="formatIt(note.value)"></p>
                        <div class="box-sizing: content-box; d-flex text-sm opacity-6 align-items-center text-white justify-content-end text-right">
                            
                            <small>@{{ momentIt(note.created_at) }}</small>
                            <small class="ml-1" v-if="note.sender_name">- @{{note.sender_name}}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div v-else class="text-center text-muted p-4">
        {{ __('No notes available') }}
    </div>
</div>

