<template>
    <div> <!--before placeholder was assigned here, cause attribute under <wysiwyg> but  not in props -->
        <input id="trix" type="hidden" :name="name" :value="value"> <!--value used as initial content for trix-editor-->
        
        <trix-editor ref="trix" input="trix" :placeholder="placeholder"></trix-editor>
    </div>
</template>

<script>
    import Trix from 'trix';
    
    export default {
        props: ['name', 'value', 'placeholder', 'shouldClear'],
        
        //created () {
        //    this.$parent.$on('created', () => this.$refs.trix.value = '');
        //},
                
        ////or inside mounted, this.$watch
        //watch: {
        //    shouldClear() {
        //        this.shouldClear ? (this.$refs.trix.value = '') : this.value;
        //    }
        //},
        
        mounted () {
            this.$refs.trix.addEventListener('trix-change', e => {
                //console.log('Handling');
                this.$emit('input', e.target.innerHTML);
            });
            
            this.$watch('shouldClear', () => {
                //this.shouldClear ? (this.$refs.trix.value = '') : this.value;
                this.$refs.trix.value = '';
            });
        }
    }
</script>
