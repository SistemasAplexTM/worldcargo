const objVue = new Vue({
    el: '#home',
    data: {
        all: 0,
        web: 0,
        sistem: 0
    },
    mounted: function(){
      this.getConsignees();
    },
    methods: {
        getConsignees: function() {
            axios.get('consignee/getConsigneesMonth').then(response => {
                this.all = response.data.all.length;
                this.web = response.data.web.length;
                this.sistem = response.data.sistem.length;
            });
        },
    }
});
