<script>
    import Replies from '../components/Replies.vue';
    import SubscribeButton from '../components/SubscribeButton.vue';
    
    export default {
        //props: ['dataRepliesCount', 'dataLocked'],
        props: ['thread'],
        
        components: { Replies, SubscribeButton },
        
        data () {
            return {
                //repliesCount: this.dataRepliesCount,
                //locked: this.dataLocked
                repliescount: this.thread.replies_count,
                locked: this.thread.locked,
                title: this.thread.title,
                body: this.thread.body,
                //form: {
                //    title: this.thread.title,
                //    body: this.thread.body,
                //}
                form: {},
                editing: false
            }
        },
        
        created () {
            this.resetForm();
        },
        
        methods: {
            //lock () {
            toggleLock () {
                //this.locked = true;                
                //axios.post('/locked-threads/' + this.thread.slug);
                
                let uri = `/locked-threads/${this.thread.slug}`;
                
                axios[this.locked? 'delete' : 'post'](uri);
                
                this.locked = ! this.locked;
            },
            
            update () {
                let uri = `/threads/${this.thread.channel.slug}/${this.thread.slug}`; //should use ziggy package instead
                
                //axios.patch(uri, {
                //    title: this.form.title,
                //    body: this.form.body 
                //})
                axios.patch(uri, this.form)                
                //.catch(error => {
                //    flash(error.response.data, 'danger');
                //    this.body = this.reply.body;
                //});
                .then(() => {
                    this.editing = false;
                    this.title = this.form.title;
                    this.body = this.form.body;
                    
                    flash('Your thread has been updated!');
                });
            },
            
            resetForm () {
                //this.form.title = this.thread.title;
                //this.form.body = this.thread.body;
                this.form = {
                    title: this.thread.title,
                    body: this.thread.body,
                };

                this.editing = false;
            }
        }
    }
</script>
