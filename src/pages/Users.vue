<template>
  <div class="users">
      <div class="search"></div>
      <div class="users-list">
          <div class="item" 
               v-for="(item, id) in users" 
               :key="`user_item_${id}`">
                <div class="img">
                    <img :src="item.avatar || noImage">
                </div>
                <div class="info">
                    <div class="name" @click="$router.push(`/users/${item.id}`)">
                        {{ item.name }} 
                        <span v-if="item.id === userId">Это вы!</span>
                    </div>
                    <div class="type">{{ item.type }}</div>
                    <div class="button" 
                         v-if="item.id !== userId">
                        <button @click="submitRequestFriend(item.id)" 
                                v-if="!requests[item.id]">
                            Добавить в друзья
                        </button>
                        <button v-else-if="requests[item.id]" 
                                @click="removeRequest(requests[item.id], item.id)">
                            Отозвать заявку
                        </button>
                    </div>
                </div>
          </div>
      </div>
  </div>
</template>

<script>
export default {
    name: 'Users',
    data() {
       return {
           users: [],
           search: '',
           noImage: require('../assets/no-image.png'),
           userId: localStorage.getItem('id'),
           requests: {}
       };
    },
    methods: {
        submitRequestFriend(friendId){
            fetch(
                '//localhost/api/notification.php',
                {
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    method: 'post',
                    body: JSON.stringify({
                        userId: this.userId,
                        otherId: friendId,
                        type: 'ADD_FRIEND'
                    })
                }
            ).then(res => res.json()).then(res => {
                // console.log(res);
                const requests = {...this.requests};
                requests[friendId] = res.id;
                this.requests = {...requests};
            });  
        },
        removeRequest(notificationId, friendId){
            fetch(
                `//localhost/api/notification.php?id=${notificationId}`,
                {
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    method: 'delete'
                }
            ).then(res => res.json()).then(res => {
                if(res.res){
                    const requests = {...this.requests};
                    delete requests[friendId];
                    this.requests = {...requests};
                }
            });
        }
    },
    mounted() {
        fetch(
            '//localhost/api/users/index.php',
            {
                headers: {
                    'Content-Type': 'application/json'
                }
            }
        ).then(res => res.json()).then(res => {
            console.log(res);
            this.users = res.users || [];
            this.requests = res.requests || {};
        });
    }
}
</script>

<style lang="scss" scoped>
.users {
    width: 100%;
    .search{}
    &-list{
        display: flex;
        flex-direction: column;
        margin: 2rem;
        // width: 100%;
        .item {
            display: flex;
            width: 100%;
            box-shadow: 0 0 16px rgba(0,0,0,.2);
            border-radius: 4px;
            padding: 2rem;
            box-sizing: border-box;
            &:not(:first-child){
                margin-top: 2rem;
            }
            .img {
                max-height: 120px;
                min-height: 120px;
                // height: 100%;
                // width: auto;
                max-width: 120px;
                min-width: 120px;
                display: flex;
                align-items: center;
                justify-content: center;
                img {
                    width: auto;
                    height: 100%;
                }
            }
            .info {
                display: flex;
                flex-direction: column;
                margin-left: 2rem;
                width: 100%;
                .name {
                    font-size: 2rem;
                    margin-bottom: 1rem;
                    &:hover{
                        cursor: pointer;
                    }
                    span {
                        color: green;
                        font-size: 1rem;
                        margin-left: 1rem;
                    }
                }
                .type {
                    font-weight: 500;
                    font-size: 1.1rem;
                    font-style: italic;
                }
                .button {
                    margin-top: auto;
                    display: flex;
                    align-items: flex-end;
                    justify-content: flex-end;
                    button{
                        border: none;
                        background-color: blue;
                        color: white;
                        padding: .6rem 1rem;
                        font-size: 1.1rem;
                        border-radius: 3px;
                    }
                }
            }
        }
    }
}
</style>