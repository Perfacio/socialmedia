<template>
  <div class="user">
      <div class="info">
          <div class="avatar">
              <img :src="user.avatar || noImage" :alt="user.name"/>
          </div>
          <h3>Друзья ({{ user.friends.length }})</h3>
          <div class="friends">
              <div class="item"     
                   v-for="(item, id) in user.friends" 
                   :key="`user_${id}`">
                <router-link :to="`/users/${item.id}`">
                    <img :src="item.avatar || noImage" alt=""/>
                </router-link>
              </div>
          </div>
      </div>
      <div class="desc">
          <h1>{{ user.name }}
              <span :class="user.status && user.status === 'online' ? 
                                        'online' : 
                                        'offline' ">
                  {{ user.status && user.status === 'online' ? 
                                        'онлайн' : 
                                        'офлайн' 
                  }}
                </span>
            </h1>
          <h3>{{ user.type }}</h3>
          <div class="date">
              Дата открытия: <span>{{user.dateOpen}}</span>
          </div>
      </div> 
  </div>
</template>

<script>
export default {
    name: 'User',
    data() {
        return {
            user: {},
            noImage: require('../assets/no-image.png')
        }
    },
    mounted() {
        fetch(
            `//localhost/api/user/user.php?id=${this.$route.params.id}`,
            {
                headers: {
                    'Content-Type': 'application/json'
                }
            }
        ).then(res => res.json()).then(res => {
            this.user = res.user;
            console.log(this.user);
        });
    }
}
</script>

<style lang="scss" scoped>
    .user {
        display: flex;
        width: 100%;
        .info {
            width: 30%;
            box-sizing: border-box;
            margin: 2rem 2rem 0;
            padding: .5rem;
            border-radius: 3px;
            box-shadow: 0 0 8px rgba(0,0,0,.2);
            background-color: #fff;
            .avatar {
                width: 90%;
                // max-height: 300px;
                margin: 0 auto;
                img{
                    width: 100%;
                    height: auto;
                }
            }
            .friends {
                display: flex;
                flex-wrap: wrap;
                justify-content: space-between;
                // align-items: center;
                .item {
                    width: 30%;
                    img {
                        width: 100%;
                        height: auto;
                    }
                }
            }
        }
        .desc {
            width: 70%;
            box-sizing: border-box;
            margin: 2rem 2rem 0;
            // padding: .5rem;
            h1 {
                margin: 0;
                padding: 0;
                line-height: 1;
                font-size: 2rem;
                font-weight: 500;
                display: flex;
                span {
                    font-size: .8rem;
                    margin-left: 1rem;
                    &.online {
                        color: green;
                    }
                    &.offline {
                        color: red;
                    }
                }
            }
            h3 {
                font-weight: 400;
                font-size: 1.3rem;
                margin: 1rem 0 1rem;
                padding-bottom: 2rem;
                border-bottom: 1px solid grey;
            }
            .date {
                span {
                    font-size: 1.2rem;
                    font-weight: 500;
                }
            }
        }
    }
</style>