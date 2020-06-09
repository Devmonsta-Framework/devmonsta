jQuery(window).on('dimention.dm', function(){
    Vue.component('dm-dimensions', {
        props: ["dimension", "linkedName", "name"],
        template: `
            <ul class="dm-option-dimensions">
                <slot></slot>
                <li>
                    <button @click.prevent="linkedDimensions" class="dm-option-input dm-dimension-btn" :class="{active: isDimension}"><i class="fas fa-link"></i></button>
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
    
    Vue.component('dm-dimensions-item', {
        props: ["name", "value", "label"],
        template: `
            <li>
                <input class="dm-option-input dm-dimension-number-input input-top" type="number" :name="name" v-model="inputValue" min="0"/>
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
    })

   
})



jQuery(window).on('load',function($){
    jQuery(window).trigger('dimention.dm')

    // $(".dm-dimension-attachment-input").on("click", function(e){
    //     e.preventDefault();
    //     var current_div = $(this);
    //     //toggle class on clicking isLinked button
    //     current_div.toggleClass('clicked');

    //     //change value of hidden field to store clicked value
    //     current_div.hasClass('clicked') ?  
    //             current_div.siblings(".dm-dimension-linked-input").val('1'):
    //             current_div.siblings(".dm-dimension-linked-input").val('0'); 

    //     //change isLinked button background color on clicking
    //     current_div.hasClass('clicked') ?  
    //             current_div.css("background-color","gray"):
    //             current_div.css("background-color","white"); 
       
    //     // update values of all inputs on clickng isLinked button
    //     if(current_div.hasClass('clicked')){
    //         let fixed_value = parseInt(current_div.siblings(".input-top").val());
    //         current_div.siblings(".input-top").val(fixed_value);
    //         current_div.siblings(".input-right").val(fixed_value);
    //         current_div.siblings(".input-bottom").val(fixed_value);
    //         current_div.siblings(".input-left").val(fixed_value);
    //     }
    // });
    


});