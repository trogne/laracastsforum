<template>
    <div>
        <div v-if="signedIn">
            <div class="form-group">
                <!--<textarea name="body"-->
                <!--          id="body"-->
                <!--          class="form-control"-->
                <!--          placeholder="Have something to say?"-->
                <!--          rows="5"-->
                <!--          required-->
                <!--          v-model="body"></textarea>-->
                <!--<wysiwyg name="body" v-model="body" placeholder="Have something to say?" ref="trix"></wysiwyg>-->
                <wysiwyg name="body" v-model="body" placeholder="Have something to say?" :shouldClear="completed"></wysiwyg>
            </div>
            <button type="submit"
                    class="btn btn-default"
                    @click="addReply">Post</button>
        </div>
        <p class="text-center" v-else>
            Please <a href="/login">sign in</a> to participate in this discussion.
        </p>
    </div>
</template>

<script>
    import 'jquery.caret';
    import 'at.js';

    export default {
        //props: ['endpoint'],
        
        data() {
            return {
                body: '',
                completed: false
            }
        },
        
        //computed: {
        //    signedIn() {
        //        return window.App.signedIn;
        //    }
        //},
        
        mounted() {
            this.$emit('maxwell', 'marcel');
            
            $('#body').atwho({
                at: "@",
                delay: 750,
                callbacks: {
                    remoteFilter: function(query, callback) {
                        $.getJSON('/api/users', {name: query}, function(usernames) {
                            callback(usernames);
                        })
                    }
                },
            });
        },

        methods: {
            addReply() {
                //axios.post(this.endpoint, { body: this.body })
                axios.post(location.pathname + '/replies', { body: this.body })
                    .catch(error => {
                        flash(error.response.data, 'danger');
                        //flash(error.response.data.errors.body[0], 'danger'); //WITHOUT try/catch
                    })
                    .then(({data}) => { //destructuring data from the response
                        this.body = '';

                        //document.querySelector('trix-editor').value = '';
                        //this.$refs.trix.$refs.trix.value =  ''; //1st this.$refs = wysiwyg... then the ref of the child
                        //see also event in Wysiwyg.vue
                        this.completed = true;
                        
                        flash('Your reply has been posted.');
                        
                        this.$emit('created', data);
                    });
            }
        }
    }
</script>