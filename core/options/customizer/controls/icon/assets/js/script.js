Vue.component('dm-icon-picker', {
    props: ["icon_list", "name", "default_icon_type", "default_icon"],
    template: `
        <div class="dm-icon-control">
            <div class="dm-select-icon">
                <div class="dm-icon-box">
                    <div :class="iconBox" @click="openModal">
                        <span :class="'dm-icon ' + savedIconClass"></span>
                        <div class="dm-placeholder-icons">
                                <i class="fas fa-ad"></i>
                                <i class="far fa-address-book"></i>
                                <i class="fab fa-affiliatetheme"></i>
                        </div>
                   </div>
                   <div class="dm-close-icon" @click="removeIcon" v-if="savedIconClass"><i class="fas fa-times"></i></div>
                </div>
                <button class="dm-add-icon-btn button" @click.prevent="openModal">{{ iconBtnText }}</button>
                <input type="hidden" :name="name" v-model="savedIconClass">
                <input type="hidden" :name="name + '_type'" :value="iconType">
            </div>
            <transition name="fade">
                <dm-icon-modal v-if="showModal" :iconList="iconList" :default_icon_type="default_icon_type" :default_icon="default_icon" @picked-icon="pickedIconClass" @close-modal="closeModal" @save-icon="saveIcon" @icon-type="changeIconType"></dm-icon-modal>
            </transition>
        </div>
    `,
    data: function () {
        return {
            iconList: [],
            pickedIcon: '',
            savedIconClass: '',
            showModal: false,
            save: false,
            iconType: '',
            tempiconType: ''
        }
    },
    computed: {
        iconBtnText: function () {
            return this.savedIconClass ? 'Change Icon' : 'Add Icon'
        },
        iconBox: function () {
            let iconClass = 'iconBox-inner';
            if (this.savedIconClass) {
                iconClass += ' has-icon '
            }
            return iconClass;
        }
    },
    methods: {
        pickedIconClass: function (iconClass) {
            this.pickedIcon = iconClass;
        },
        openModal: function () {
            this.showModal = true;
        },
        closeModal: function () {
            this.showModal = false;
            this.save = false;
        },
        removeIcon: function () {
            this.pickedIcon = '';
            this.savedIconClass = '';
        },
        saveIcon: function () {
            this.showModal = false;
            this.save = true;
            this.savedIconClass = this.pickedIcon;
            this.iconType = this.tempiconType;
        },
        changeIconType: function (value) {
            this.tempiconType = value;
        }
    },
    created: function () {
        this.iconList = JSON.parse(this.icon_list);
        this.savedIconClass = this.default_icon ? this.default_icon : '';
        this.iconType = this.default_icon_type ? this.default_icon_type : '';
    }
});

Vue.component('dm-icon-modal', {
    props: ["iconList", "default_icon_type", "default_icon"],
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
                        <option :value="icon.id" v-for="icon in iconList">{{ icon.name }}</option>
                    </select>
                    <input type="text" placeholder="serach..." class="dm-icon-search" v-model="search">
                </div>
                <dm-icon-list v-if="icons.length" :icons="icons" :search="search" @picked-icon="pickedIcon" :default_icon="default_icon"></dm-icon-list>
                <div class="dm-icon-modal-footer">
                    <button class="button media-button button-primary button-large media-button-0" @click.prevent="$emit('save-icon')">Save</button>
                </div>
            </div>
        </div>
    `,
    data: function () {
        return {
            search: '',
            iconType: ''
        }
    },
    computed: {
        icons: function () {
            let icons = this.iconList.filter(item => item.id == this.iconType);
            return icons ? icons[0].icons : [];
        }
    },
    methods: {
        pickedIcon: function (iconClass) {
            this.$emit('picked-icon', iconClass);
        }
    },
    watch: {
        iconType: function (val) {
            this.$emit('icon-type', val);
        }
    },
    created: function () {
        this.iconType = this.default_icon_type
    }
});

Vue.component('dm-icon-list', {
    props: ["icons", "search", "default_icon"],
    template: `
        <div class="dm-list-icon">
            <ul>
                <li :data-icon="icon" v-for="icon in finalIcon" @click="pickIcon(icon)" :class="{ 'active': pickedIcon ==  icon}"><span :class="icon"></span></li>
            </ul>
        </div>
    `,
    data: function () {
        return {
            iconsCl: [],
            searchText: '',
            pickedIcon: ''
        }
    },
    methods: {
        pickIcon: function (iconClass) {
            this.pickedIcon = iconClass;
            this.$emit('picked-icon', iconClass);
        }
    },
    computed: {
        finalIcon: function () {
            return this.searchText ? this.iconsCl.filter(icon => icon.indexOf(this.searchText) > -1) : this.iconsCl;
        }
    },
    created: function () {
        this.iconsCl = this.icons;
        this.pickedIcon = this.default_icon;
    },
    watch: {
        search: function (val) {
            this.searchText = val;
        },
        icons: function (val) {
            this.iconsCl = val;
        }
    }
});