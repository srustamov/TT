<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4 offset-md-4 mt-5">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h1>{{headerMessage}}</h1>
                    </div>
                    <div class="card-body">
                        <p>
                            {{bodyMessage}}
                        </p>
                    </div>
                    <div class="card-footer d-flex justify-content-between">
                        <to link="/home" view="btn btn-primary btn-lg" title="Go Home">Go Home</to>
                        <btn type="outline-info" size="lg">click me</btn>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import btn from './ui/button.vue';
    import to from './ui/to.vue';
    export default {
        name: "Card",
        data(){
            return  {
                headerMessage:'Example Component',
                bodyMessage:'Lorem ipsum dolor sit amet, consectetur adipisicing elit.'
            }
        },
        components:{ btn ,to }
    }
</script>

<style scoped>

</style>