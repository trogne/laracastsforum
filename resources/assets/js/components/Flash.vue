<template>
    <div class="alert alert-success alert-flash" role="alert" v-show="show">
      <strong>Success!</strong> {{ body }}
    </div>
</template>

<script>
    export default {
        props: ['message'],
        
        mounted() {
            console.log('Component mounted.')
        },
        
        data(){
            return {
                body: '',
                show: false
            }
        },
        
        created() {
            if (this.message) {
                //this.body = this.message;
                //this.show = true;
                this.flash(this.message);
                
                //setTimeout(() => {
                //    this.show = false;
                //}, 3000);
            }
            
            window.events.$on('flash', message => this.flash(message)); //global event bus
        },
        
        methods: {
            //$('.alert-flash').delay(3000).fadeOut(); //NO, we want a clear data-driven approach
            flash(message) {
                this.body = message;
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
