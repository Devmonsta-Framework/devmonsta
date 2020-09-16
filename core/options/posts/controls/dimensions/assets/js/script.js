jQuery(window).on('dimensions.dm', function(){
    Vue.component('devm-dimensions', {
        props: ["dimension", "linkedName", "name"],
        template: `
            <ul class="devm-option-dimensions">
                <slot></slot>
                <li>
                    <button @click.prevent="linkedDimensions" class="devm-option-input devm-dimension-btn" :class="{active: isDimension}"><span class="dashicons dashicons-admin-links"></span></button>
                    <input type="hidden" :name="linkedName" v-model="isDimension" />
                    <input v-if="name" type="hidden" v-model="message" :data-customize-setting-link="name"  />
                    <label>&nbsp;</label>
                </li>
            </ul>
        `,
        data: function(){
            return {
                isDimension: true,
                message: "hello"
            }
        },
        watch: {
            message: function(val){
                if(val && wp.customize){
                    wp.customize( this.name, function ( obj ) {
                        obj.set( val );
                    } );
                }
            }
        },
        methods: {
            linkedDimensions: function(){
                this.isDimension = !this.isDimension
            }
        },
        mounted: function(){
            var self = this;
            this.isDimension = this.dimension;

            this.$on('input-change', function(val){
                var dimentionData = {isLinked: this.isDimension};
                this.$children.forEach(function(item){
                    if(self.isDimension == true){
                        item.inputValue = val;
                    }
                    dimentionData[item.label.toLowerCase().replace('/\s+/', '_')] = self.isDimension == true ? val : item.inputValue;
                });
                this.message = JSON.stringify(dimentionData);
            });
        }
    });
    
    Vue.component('devm-dimensions-item', {
        props: ["name", "value", "label"],
        template: `
            <li>
                <input class="devm-option-input devm-dimension-number-input input-top" type="number" :name="name" v-model="inputValue" min="0"/>
                <label>{{label}}</label>
            </li>
        `,
        data: function(){
            return {
                inputValue: ''
            }
        },
        watch: {
            inputValue: function(val){
                this.$parent.$emit('input-change', val);
            }
        },
        created: function(){
            this.inputValue = this.value;
        }
    });
})



jQuery(document).on('ready',function($){
    jQuery(window).trigger('dimensions.dm')
});