<script>
    import Replies from '../components/Replies.vue';
    import SubscribeButton from '../components/SubscribeButton.vue';
    
    export default {
        //props: ['dataRepliesCount', 'dataLocked'],
        props: ['thread'],
        
        components: { Replies, SubscribeButton },
        
        data() {
            return {
                //repliesCount: this.dataRepliesCount,
                //locked: this.dataLocked
                repliescount: this.thread.replies_count,
                locked: this.thread.locked
            }
        },
        
        methods: {
            //lock () {
            toggleLock () {
                //this.locked = true;                
                //axios.post('/locked-threads/' + this.thread.slug);
                
                axios[this.locked? 'delete' : 'post']('/locked-threads/' + this.thread.slug);
                
                this.locked = ! this.locked;
            }
        }
    }
</script>
