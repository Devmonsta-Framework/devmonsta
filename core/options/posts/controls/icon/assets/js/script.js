Vue.component('dm-icon-picker',{
    props: ["icon_list", "name", "default_icon_type"],
    template: `
        <div class="dm-icon-control">
            <div class="dm-select-icon">
                <div class="dm-icon-box">
                    <i :class="'current-icon ' + pickedIcon"></i>
                </div>
                <button class="dm-add-icon-btn button" @click="openModal">Add Icon</button>
                <input type="hidden" :name="name" v-model="pickedIcon">
            </div>
            <dm-icon-modal v-if="showModal" :iconList="iconList" :default_icon_type="default_icon_type" @picked-icon="pickedIconClass" @close-modal="closeModal" @save-icon="saveIcon"></dm-icon-modal>
        </div>
    `,
    data: function(){
        return {
            iconList: [],
            pickedIcon: '',
            showModal: false,
            save: false
        }
    },
    methods: {
        pickedIconClass: function(iconClass){
            this.pickedIcon = iconClass;
        },
        openModal: function(){
            this.showModal = true;
        },
        closeModal: function(){
            this.showModal = false;
        },
        saveIcon: function(){
            this.showModal = false;
            this.save = true;
        }
    },
    created: function(){
        this.iconList = JSON.parse(this.icon_list);
    }
});

Vue.component('dm-icon-modal', {
    props: ["iconList", "default_icon_type"],
    template: `
        <div class="dm-icon-modal-container">
            <div class="dm-icon-modal-data">
                <div class="dm-icon-modal-header">
                    <ul>
                        <li>Icon Fonts</li>
                    </ul>
                    <div class="dm-icon-modal-close" @click="$emit('close-modal')"><i class="fas fa-times"></i></div>
                </div>
                <div class="dm-icon-modal-selection">
                    <select class="dm-icon-type" v-if="iconList.length" v-model="iconType">
                        <option :value="icon.id" v-for="icon in iconList" :selected="{ selected: icon.id == iconType }">{{ icon.name }}</option>
                    </select>
                    <input type="text" placeholder="serach..." class="dm-icon-search" v-model="search">
                </div>
                <dm-icon-list v-if="icons.length" :icons="icons" :search="search" @picked-icon="pickedIcon"></dm-icon-list>
                <div class="dm-icon-modal-footer">
                    <button class="button media-button button-primary button-large media-button-0" @click="$emit('save-icon')">Save</button>
                </div>
            </div>
        </div>
    `,
    data: function(){
        return {
            search: '',
            iconType: ''
        }
    },
    computed: {
        icons: function(){
            let icons = this.iconList.filter(item => item.id == this.iconType);
            return icons ? icons[0].icons : [];
        }
    },
    methods: {
        pickedIcon: function(iconClass){
            this.$emit('picked-icon', iconClass);
        }
    },
    created: function(){
        this.iconType = this.default_icon_type
    }
});

Vue.component('dm-icon-list', {
    props: ["icons", "search"],
    template: `
        <div class="dm-list-icon">
            <ul>
                <li :data-icon="icon" v-for="icon in finalIcon" @click="pickIcon(icon)" :class="{ 'active': pickedIcon ==  icon}"><span :class="icon"></span></li>
            </ul>
        </div>
    `,
    data: function(){
        return {
            iconsCl: [],
            searchText: '',
            pickedIcon: ''
        }
    },
    methods: {
        pickIcon: function(iconClass){
            this.pickedIcon = iconClass;
            this.$emit('picked-icon', iconClass);
        }
    },
    computed: {
        finalIcon: function(){
            return  this.searchText ? this.iconsCl.filter(icon => icon.indexOf(this.searchText) > -1) : this.iconsCl;
        }
    },
    created: function(){
        this.iconsCl = this.icons;
    },
    watch: {
        search: function(val){
            this.searchText = val;
        },
        icons: function(val){
            this.iconsCl = val;
        }
    }
});

var app = new Vue({
    el: '.dm-vue-app',
    data: {
        icons: []
    },
    created: function(){
        this.icons = dmIcons;
    }
})