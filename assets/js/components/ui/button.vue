<template>
    <button class="btn" :class="'btn-'+type+' btn-'+size"><slot></slot></button>
</template>

<script>
    export default {
        props:{
            'type':{
                type:String,
                default:'default'
            },
            'size':{
                type:String,
                default:'sm'
            }
        },
        name: "button"
    }
</script>

<style scoped>

</style>