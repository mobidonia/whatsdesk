<div class="scrollable-content" style="height: calc(100vh - 100px); overflow-y: auto; padding: 20px;">
    <div class="d-flex justify-content-center align-items-center mb-4">
        <a :href="'/contacts/contacts/'+activeChat.id+'/edit'" class="profile-picture-container">
            <div v-cloak v-if="activeChat&&activeChat.name&&activeChat.name[0]&&(activeChat.avatar==''||activeChat.avatar==null)"
                class="avatar avatar-content bg-gradient-success" style="min-width:96px; min-height:96px; width:96px; height:96px; border-radius:50%">@{{activeChat.name[0]}}
            </div>
            <img v-cloak v-if="activeChat&&(activeChat.avatar!=''&&activeChat.avatar!=null)"  alt="" :src="activeChat.avatar"
                :data-src="activeChat.avatar" class="avatar position-relative" style="width:96px; height:96px; border-radius:50%; object-fit:cover" />
        </a>
    </div>

    <!-- Display the phone number as label -->
    <div class="form-group text-center mb-4">
        <a :href="'/contacts/contacts/'+activeChat.id+'/edit'"><h2 class="mb-0 text-primary">@{{activeChat.name}}</h2></a>
        <p class="text-muted">@{{activeChat.country.name}}</p>

        <div class="contactGroupLabels">
            <div v-for="group in activeChatGroups" :key="group.id" class="contactGroupLabel badge badge-primary rounded mr-2 mb-2">@{{ group.name }}</div>
        </div>
    </div>

    <h5 class="text-muted">{{ __('Assigned to')}}</h5>
    <b-dropdown size="xs" id="dropdown-right" split :text="getAssignedUser(activeChat)" variant="primary">
        <b-dropdown-item v-for="(user, key) in users" :key="key" @click="assignUser(key, activeChat.id)">
            @{{user}}
        </b-dropdown-item>
    </b-dropdown>

    <h5 class="mt-4 text-muted">{{ __('Contact Details')}}</h5>

    <div class="contacInfo border-radius-lg border p-4 mb-4">
        <div class="d-flex justify-content-between">
            <div class="contactInfoLabel">{{ __('Name') }}</div>
            <div class="contactInfoInput">@{{ activeChat.name }}</div>
        </div>
        <div class="d-flex justify-content-between">
            <div class="contactInfoLabel">{{ __('Phone') }}</div>
            <div class="contactInfoInput">@{{ activeChat.phone }}</div>
        </div>
        <div class="d-flex justify-content-between">
            <div class="contactInfoLabel">{{ __('Email') }}</div>
            <div class="contactInfoInput">@{{ activeChat.email }}</div>
        </div>
        <div class="d-flex justify-content-between">
            <div class="contactInfoLabel">{{ __('Country') }}</div>
            <div class="contactInfoInput" > @{{ activeChat.country.name }}</div>
        </div>
        <div class="d-flex justify-content-between">
            <div class="contactInfoLabel">{{ __('Suscribed') }}</div>
            <div v-cloak v-if="activeChat.subscribed == '1'"  class="contactInfoInput badge badge-success" >{{ __('Subscribed') }}</div>
            <div v-cloak v-else class="contactInfoInput badge badge-warning" >{{ __('Not subscribed') }}</div>
        </div>
    </div>

    <h5 class="text-muted">{{ __('Chat Details')}}</h5>

    <div class="contacInfo border-radius-lg border p-4">
        <div class="d-flex justify-content-between">
            <div class="contactInfoLabel">{{ __('Created at') }}</div>
            <div class="contactInfoInput" > @{{ momentDaySimple(activeChat.created_at)}}</div>
        </div>
        <div class="d-flex justify-content-between">
            <div class="contactInfoLabel">{{ __('Status') }}</div>
            <div class="d-flex align-items-center">
                <div v-cloak v-if="activeChat.resolved_chat == '0'"  class="contactInfoInput badge badge-primary" >{{ __('Open') }}</div>
                <div v-cloak v-else class="contactInfoInput badge badge-success" >{{ __('Closed') }}</div>
                <button v-if="activeChat.resolved_chat == '0'" @click="updateChatStatus(activeChat.id)" class="btn btn-sm btn-success ml-2">{{ __('Mark as Closed') }}</button>
                <button v-else @click="reopenChat(activeChat.id)" class="btn btn-sm btn-primary ml-2">{{ __('Reopen Chat') }}</button>
            </div>
        </div>
        <div class="d-flex justify-content-between">
            <div class="contactInfoLabel">{{ __('Last Activity') }}</div>
            <div class="contactInfoInput" > @{{ momentDaySimple(activeChat.updated_at )}}</div>
        </div>
        <div class="d-flex justify-content-between">
            <div class="contactInfoLabel">{{ __('Language') }}</div>
            <div class="contactInfoInput" > @{{ activeChat.language }}</div>
        </div>
        <div class="d-flex justify-content-between align-items-center">
            <div class="contactInfoLabel">{{ __('AI Bot Enabled') }}</div>
            <label class="custom-toggle">
                <input type="checkbox" 
                    v-model="activeChat.enabled_ai_bot"
                    @change="updateAIBotStatus"
                    :checked="activeChat.enabled_ai_bot == '1'">
                <span class="custom-toggle-slider rounded-circle" data-label-off="No" data-label-on="Yes"></span>
            </label>
        </div>
    </div>

    <!-- Show the Custom fields -->
    <h5 class="text-muted mt-4">{{ __('Custom Fields')}}</h5>
    <div class="contacInfo border-radius-lg border p-4">
        <div v-for="field in activeChatCustomFields" class="d-flex justify-content-between" :key="field.id">
            <div class="contactInfoLabel">@{{ field.name }}</div>
            <div class="contactInfoInput" > @{{ field.value }}</div>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-4">
        <a :href="'/contacts/contacts/'+activeChat.id+'/edit'" class="btn btn-primary btn-block"><i class="ni ni-ruler-pencil"></i> {{ __('Edit Contact') }}</a>
    </div>
</div>