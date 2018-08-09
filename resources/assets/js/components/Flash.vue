<template>
    <div class="alert alert-flash"
        :class="'alert-'+level"
        role="alert"
        v-show="show"
        v-text="body">
    </div>   
</template>   /*v-text="body" au lieu de {{ body }} inline*/

<script>
    export default {
        props: ['message'],
        
        //mounted() {
        //    console.log('Component mounted.')
        //},
        
        data(){
            return {
                body: this.message,
                level: 'success',
                show: false
            }
        },
        
        created() {
            if (this.message) {
                //this.body = this.message;
                //this.show = true;
                //this.flash(this.message);
                //this.flash(JSON.parse(this.message)); //si dans middleware: json_encode([...]
                //if (/^[\],:{}\s]*$/.test(this.message.replace(/\\["\\\/bfnrtu]/g, '@').
                //replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']').
                //replace(/(?:^|:|,)(?:\s*\[)+/g, ''))) { // check if json formatted string
                //    this.flash(JSON.parse(this.message));
                //}else{
                //    this.flash();
                //}
                this.flash();
                
                //setTimeout(() => {
                //    this.show = false;
                //}, 3000);
            }
            
            window.events.$on(  //global event bus
                'flash', data => this.flash(data) //this.flash = le flash de ce component
            );
        },
        
        methods: {
            //$('.alert-flash').delay(3000).fadeOut(); //NO, we want a clear data-driven approach
            flash(data) {
                if (data) {
                    this.body = data.message;
                    this.level = data.level;                    
                }
                
                this.show = true;
                
                this.hide();
            },
            
            hide() {
                setTimeout(() => {
                    this.show = false; //data-centric approach (a boolean to toggle its visibility)
                }, 3000);                
            }
        }
    }
</script>

<style>
    .alert-flash {
        position: fixed;
        right: 25px;
        bottom: 25px;
    }
</style>
