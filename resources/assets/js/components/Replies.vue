<template>
    <div>
         <!--tag= creates a div tag (no tag creation with "transition")-->
           <!-- <reply :data="reply" @deleted="remove(index)" :canUpdate="canUpdate"></reply>-->
        <!--<transition-group :name="animToUse" tag="div">-->
        <!--    <div v-for="(reply, index) in items" :key="reply.id">-->
        <!--        <reply :data="reply" @deleted="remove(index)"></reply>-->
        <!--    </div>-->
        <!--</transition-group>-->
        
        <!--now transition in Reply.vue-->
        <div v-for="(reply, index) in items" :key="reply.id">
            <reply :data="reply" @deleted="remove(index)"></reply>
        </div>
        
        <paginator :dataSet="dataSet" @changed="fetch"></paginator>
        
        <!--<new-reply :endpoint="endpoint" @created="add"></new-reply>-->
        <new-reply @created="add"></new-reply>
    </div>
</template>

<script>
    import Promise from 'promise-polyfill';
    if (!window.Promise) { window.Promise = Promise }; //FOR IE !!!! "Edge can't reach... vbox problem..." fuck off

    import Reply from './Reply.vue';
    import NewReply from './NewReply.vue';
    import collection from '../mixins/collection'

    export default {
        //props: ['data', 'canUpdate'],
        //props: ['data'],
        
        components: { Reply, NewReply },
        
        mixins: [collection],
        
        data() {
            return {
                //items: this.data, //pas necessaire, peut faire "v-for="(reply, index) in data"
                //items: [], //getting that from mixins/collection
                dataSet: false
                //endpoint: location.pathname + '/replies',
                //animToUse: 'list' //non, maintenant transition dans Reply.vue
            }
        },
        
        created() { //event hook
            this.fetch();
            ////TO RE-FETCH ON BACK BUTTON : 
            //window.fetch = this.fetch;
            //fetch();
            //window.onpopstate = function(event) { //fetch MUST BE GLOBAL
            //    fetch(+location.search.match(/page=(\d+)/)[1]); //cast to int mais pas necessaire,   using unary plus LOL, au lieu de Number, parseInt, Math.round, Math.floor(parseFloat(...)) //console.log(`fetched page: ${+location.search.match(/page=(\d+)/)[1]}`);
            //}            
        },
        
        methods: {
            fetch(page, total = 1, per_page = 1, adding = false, remove = false) {
                if (total%per_page==0 && adding && total!=0) page++;
                if (total%per_page==1 && remove) page--;
                //this.animToUse = ''; //was for transition-group
                axios.get(this.url(page))
                    .then(this.refresh);
            },
            
            url(page) {
                if (!page) {
                    let query = location.search.match(/page=(\d+)/);
                    page = query ? query[1] : 1;
                }
                return `${location.pathname}/replies?page=${page}`;
            },
            
            refresh({data}) {
                this.dataSet = data;
                this.items = data.data;
                
                window.scrollTo(0, 0);
            }
            
            ////now mixins
            //add(reply){
            //    this.items.push(reply);
            //    this.$emit('added');
            //},
            //
            //remove(index) {
            //    //console.log(this.$el.querySelector('#reply-'+replyid));
            //    //this.items = this.items.filter(item => item.id != replyid); //marche pas, ptête car vue est trop dépendant des index...
            //
            //    //$(this.$children[index].$el).fadeOut(700, () => {
            //    //    flash('Reply was deleted!');
            //    //    this.$emit('remove');
            //    //});
            //
            //    this.items.splice(index, 1);
            //    flash('Reply was deleted!');
            //    this.$emit('remove');
            //}
        }
    }
</script>
