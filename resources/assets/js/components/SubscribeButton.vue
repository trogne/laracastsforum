<template>
    <button :class="classes" @click="subscribe">Subscribe</button>
</template>

<script>
    export default {
        props: ['active'],
        
        data() {
            return {
                activeb: this.active // pour éviter "Avoid mutating a prop directly"
            };
        },
        
        computed: {
            classes() {
                return ['btn', this.activeb ? 'btn-primary' : 'btn-default'];
            }
        },
        
        methods: {
            subscribe() {
                
                axios[
                    this.activeb ? 'delete' : 'post'
                ](location.pathname + '/subscriptions'); // .then(flash('Subscribed'));
             
                this.activeb = ! this.activeb;
                   
                //this.active ? flash('Subscribed') : flash('Unsubscribed');
            }
        }
    }
</script> 