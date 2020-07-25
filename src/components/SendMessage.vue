<template>
    <div>
        <textarea v-model="message"></textarea>
        <div></div>
    </div>
</template>

<script>
    export default {
        name: 'SendMessage',
        data() {
            return {
                message: ''
            };
        },
        methods: {
            send() {
                fetch('', {}).then(res=>res.json()).then(res => {
                    this.$emit('send', this.message);
                    this.message = '';
                }).catch();
            }
        }
    }
</script>

<style lang="scss" scoped>

</style>