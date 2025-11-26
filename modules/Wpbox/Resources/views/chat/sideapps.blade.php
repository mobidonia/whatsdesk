<div class="sideBarApps d-flex h-100 w-100 border-radius-lg  border-left-0" >
    <div id="sideBarButtons" class="card d-flex py-4 px-3 border-radius-lg border border-left-0" >
        <div class="d-flex flex-column align-items-center justify-content-start" style="gap:1rem;">
            @foreach($sidebarModules as $module)
                <button class="btn btn-icon sideBarIconApps mr-0" @click="toggleSideApp('{{$module['alias']}}','{{$module['name']}}')" style="background-color:{{$module['brandColor']}}; width: 50px; height: 50px; border-radius: 13px; {{$loop->last ? 'margin-left: -5px;' : ''}}">
                    <img src="{{$module['icon']}}" class="img-fluid" style="max-width: 25px; max-height: 25px; object-fit: cover;">
                </button>
            @endforeach
                
        </div>
    </div>
    <div class="currentSideApp card h-100 w-100 border border-left-0 border-radius-lg" v-show="currentSideApp" v-cloak style="min-width:25rem; max-width:25rem;">
        <div class="card-header bg-transparent border-bottom-0 d-flex justify-content-between align-items-center">
            <div class="d-flex justify-content-between align-items-center w-100">
                <h3 class="mb-0">@{{ currentSideAppName }}</h3>
                <button class="btn btn-sm btn-outline-secondary" @click="toggleSideApp(currentSideApp,currentSideAppName)">
                    <span class="btn-inner--icon"><i class="ni ni-button-power"></i></span>
                </button>
            </div>
            
            

            
        </div>

       
        <div class="card-body" style="padding-top: 0px !important;" >
            @foreach($sidebarModules as $module)
                <div class="currentSideApp" v-if="currentSideApp === '{{$module['alias']}}'">
                    @include($module['view'])
                </div>
            @endforeach
        </div>
    </div>
</div>
