Vue.component('dm-icon-picker',{
    props: ["icon_list", "name"],
    template: `
        <div class="dm-icon-control">
            <div class="dm-select-icon">
                <div class="dm-icon-box">
                    <i class="current-icon fas fa-eye"></i>
                </div>
                <button class="dm-add-icon-btn button">Add Icon</button>
                <input type="hidden" :name="name">
            </div>
            <dm-icon-modal :iconList="iconList"></dm-icon-modal>
        </div>
    `,
    data: function(){
        return {
            iconList: []
        }
    },
    created: function(){
        this.iconList = JSON.parse(this.icon_list)
    }
});

Vue.component('dm-icon-modal', {
    props: ["iconList"],
    template: `
        <div class="dm-icon-modal-container">
            <div class="dm-icon-modal-data">
                <div class="dm-icon-modal-header">
                    <ul>
                        <li>Icon Fonts</li>
                    </ul>
                </div>
                <div class="dm-icon-modal-selection">
                    <select class="dm-icon-type" v-if="iconList.length">
                        <option :value="icon.name.toLowerCase().replace(/\s/g, '-')" v-for="icon in iconList">{{ icon.name }}</option>
                    </select>
                    <input type="text" placeholder="serach..." class="dm-icon-search">
                </div>
                <dm-icon-list v-if="iconList.length" v-for="icon in iconList" :icons="icon.icons"></dm-icon-list>
                <div class="dm-icon-modal-footer">
                    <button class="button media-button button-primary button-large media-button-0">Save</button>
                </div>
            </div>
        </div>
    `,
});

Vue.component('dm-icon-list', {
    props: ["icons"],
    template: `
        <div class="dm-list-icon">
            <ul>
                <li :data-icon="icon" v-for="icon in icons"><span :class="icon"></span></li>
            </ul>
        </div>
    `
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