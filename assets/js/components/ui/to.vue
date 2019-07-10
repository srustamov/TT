<template>
    <a :href="link" :title="title" :class="view"><slot></slot></a>
</template>

<script>
    export default {
        props:{
            link:{
                type:String,
                default:'',
            },
            title:{
                type:String,
                default: ''
            },
            view:{
                type:String,
                default:''
            }
        },
        name: "to"
    }
</script>

<style scoped>

</style>