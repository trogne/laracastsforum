//mixin is like a trait in laravel
export default {
    data() {
        return {
            items: []
        };
    },

    methods: {
        add(item){
            //this.items.push(item); //sert pu a rien car re-fetch
            this.$emit('added');
            this.fetch(this.dataSet.last_page, this.dataSet.total, this.dataSet.per_page, true);
        },
        
        remove(index) {
            //this.animToUse = 'list';
            //this.items.splice(index, 1); //sert pu a rien car re-fetch, et aussi que je n'utilise plus transition-group
            this.$emit('remove');
            this.fetch(this.dataSet.current_page, this.dataSet.total, this.dataSet.per_page, false, true); //NON!! async!
            //setTimeout(() => this.fetch(this.dataSet.current_page, this.dataSet.total, false, true), 2000); //need settimeout if splicing before
        }
    }
}
