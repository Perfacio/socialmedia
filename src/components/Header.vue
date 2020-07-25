<template>
    <header>
        <div class="menu" :class="{open: isMenuOpen}" v-if="isLogin">
            <span @click="isMenuOpen = !isMenuOpen"></span>
            <nav>
                <router-link to="/profile">Профиль</router-link>
                <router-link to="/users">Пользователи</router-link>
            </nav>
        </div>
        <div class="notifications" 
             v-if="isLogin" 
             :class="{open: (isNoticeOpen && notifications.length > 0)}">
            <img :src="notificationImg" 
                 @click="isNoticeOpen = !isNoticeOpen"/>
            <span class="count">{{ notifications.length }}</span>
            <div>
                <div class="item" 
                     v-for="(item, id) in notifications" 
                     :key="`notice_${id}`">
                    <div>
                        <span v-if="item.type === 'ADD_FRIEND'">
                            Вас хотят добавить в друзья
                        </span>
                    </div>
                    <div v-if="item.type === 'ADD_FRIEND'">
                        <button @click="acceptFriend(id)">Принять</button>
                        <button @click="removeNotice(id)">Отклонить</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="login">
            <span @click="isFormOpen = !isFormOpen" v-if="!isLogin">Войти</span>
            <span @click="logout" v-else>Выйти</span>
            <form :class="{open: isFormOpen}">
                <input type="text" placeholder="Введите логин" v-model="login"/>
                <input type="password" placeholder="Введите пароль" v-model="password"/>
                <button @click="authorize">Войти</button>
            </form>
        </div>
    </header>
</template>

<script>
export default {
   name: 'Header',
   data() {
       const isLogin = localStorage.getItem('id') && localStorage.getItem('login');

        if(isLogin){
            this.setStatus('online', localStorage.getItem('id'));
        }

       return {
           isMenuOpen: false,
           isFormOpen: false,
           login: '',
           password: '',
           isLogin: isLogin,
           notificationImg: require('../assets/notification.svg'),
           notifications: [],
           isNoticeOpen: false
       };
   },
    // computed: {
    //    isLogin() {
    //        return localStorage.getItem('id') && localStorage.getItem('login');
    //    }
    // },
   methods: {
       authorize(event) {
           event.preventDefault();
           fetch(
               '//localhost/api/user/authorize.php',
               {
                   method: 'post',
                   headers: {
                       'Content-Type': 'application/json'
                   },
                   body: JSON.stringify({
                       login: this.login,
                       password: this.password
                   })
               }
           ).then(res => res.json()).then(res => {
               // console.log(res);
               this.isFormOpen = false;
               this.isLogin = true;
               localStorage.setItem('id', res.userId);
               localStorage.setItem('login', this.login);
               this.setStatus('online', res.userId);
           });
       },
       logout() {
           this.setStatus('offline', localStorage.getItem('id'));
           localStorage.setItem('id', '');
           localStorage.setItem('login', '');
           this.isLogin = false;
           this.login = '';
           this.password = '';
       },
       acceptFriend(id) {
           fetch(
               `//localhost/api/users/friend.php`,
               {
                    headers: {
                       'Content-Type': 'application/json'
                   },
                   method: 'post',
                   body: JSON.stringify({
                       userId: this.notifications[id].userId,
                       friendId: this.notifications[id].otherId
                   })
               }
           ).then(res => res.json()).then(() => {
               this.removeNotice(id);
           });
       },
       removeNotice(id) {
            fetch(
                `//localhost/api/notification.php?id=${this.notifications[id].id}`,
                {
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    method: 'delete'
                }
            ).then(res => res.json()).then(() => {
                this.notifications.splice(id, 1);
            });
       },
       setStatus(status, userId) {
           fetch(
                `//localhost/api/status.php`,
                {
                    method: 'post',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        status,
                        userId
                    })
                }
            );
       }
   },
   mounted() {
       fetch(
           `//localhost/api/notification.php?id=${localStorage.getItem('id')}`,
           {
               headers: {
                    'Content-Type': 'application/json'
                }
           }
       ).then(res => res.json()).then(res => {
            this.notifications = res.notifications || [];
       });   
    },
    destroyed() {
        this.setStatus('offline', localStorage.getItem('id'));
    }
}
</script>

<style lang="scss" scoped>
    header {
        box-shadow: 0 0 8px 0 black;
        width: 100%;
        padding: 8px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-sizing: border-box;
        background-color: white;
        > div {
            &.menu {
                position: relative;
                > span {
                    position: relative;
                    height: 30px;
                    width: 25px;
                    display: flex;
                    &:after, &:before {
                        transition: .2s;
                        transition-delay: .15s;
                        content: '';
                        width: 25px;
                        height: 3px;
                        background-color: rgba(24, 5, 5, 0.8);
                        border-radius: 5px;
                        position: absolute;
                        left: 0;
                    }
                    &:after {
                        top: 35%;
                    }
                    &:before {
                        top: 65%;
                    }
                }
                > nav {
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    position: absolute;
                    left: 0;
                    border-radius: 3px;
                    height: 0%;
                    overflow: hidden;
                    transition: .2s;
                    > a {
                        text-decoration: none;
                        color: black;
                        padding: 8px 8px;
                        min-width: 100px;
                        transition: .3s;
                        &:hover {
                            cursor: pointer;
                            background-color: rgba(120, 120, 120, .3);
                            transition: .3s;
                        }
                    }
                }
                &.open {
                    > span {
                        &:after, &:before {
                            width: calc(25px / 1.4);
                            top: calc(50% - 2px);
                            transition: top .05s;
                        }
                        &:after {
                            left: 0;
                            transform: rotate(45deg);
                            transition: transform .2s;
                        }
                        &:before{
                            left: calc(100% - (25px / 1.4) + 4px);
                            transform: rotate(-45deg);
                            transition: transform .2s;
                        }
                    }
                    > nav {
                        height: 152px;
                        box-shadow: 0 4px 8px 0 black;
                        background-color: white;
                        transition: .2s;
                        transition-delay: .15s;
                    }
                }
            }
            &.login {
                span{
                    padding: 4px;
                    transition: .3s;
                    border-radius: 2px;
                    &:hover {
                        cursor: pointer;
                        background-color: rgba(120, 120, 120, .3);
                        transition: .3s;
                    }
                }
                > form {
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    position: absolute;
                    right: 0;
                    border-radius: 3px;
                    height: 0;
                    overflow: hidden;
                    transition: .2s;
                    &.open {
                        height: 130px;
                        box-shadow: 0 4px 8px 0 black;
                        background-color: white;
                        transition: .2s;
                        padding: 1rem;
                    }
                    input {
                        border: 2px solid rgba(110,110,130,.8);
                        padding: 8px 4px;
                        border-radius: 3px;
                        font-size: 1rem;
                        &.error {
                            border-color: red;
                        }
                        &:first-child{
                            margin-bottom: 5px;
                        }
                    }
                    button {
                        display: flex;
                        margin: 7px auto 0;
                        border: 2px solid rgba(110,110,130,.8);
                        padding: 10px 8px;
                        text-transform: uppercase;
                        border-radius: 2px;
                        font-size: 0.9rem;
                        font-weight: 500;
                        &:hover {
                            cursor: pointer;
                        }
                    }
                }
            }
            &.notifications {
                display: flex;
                position: relative;
                img {
                    width: 20px;
                    &:hover {
                        cursor: pointer;
                    }
                }
                span.count {
                    border-radius: 50%;
                    background-color: blue;
                    color: #fff;
                    right: -10px;
                    top: -5px;
                    position: absolute;
                    font-size: 10px;
                    padding: 3px 5px;
                    font-weight: 600;
                    line-height: 10px;
                }
                > div {
                    display: none;
                    position: absolute;
                    top: calc(100% + 5px);
                    width: 350px;
                    left: -175px;
                    box-shadow: 0 4px 8px 0 black;
                    background-color: #fff;
                    padding: .5rem;
                    > .item {
                        display: flex;
                        width: 100%;
                        justify-content: space-between;
                        align-items: center;
                        > div:last-child {
                            display: flex;
                            flex-direction: column;
                            > button{
                                background-color: blue;
                                color: #fff;
                                border-radius: 3px;
                                padding: .3rem .5rem;
                                border: none;
                                &:hover {
                                    cursor: pointer;
                                }
                                &:first-child{
                                    margin-bottom: 5px;
                                }
                            }
                        }
                        &:not(:last-child) {
                            padding-bottom: .5rem;
                            margin-bottom: .5rem;
                            border-bottom: 1px solid grey;
                        }
                    }
                }
                &.open {
                    > div {
                        display: flex;
                    }
                }
            }
        }
    }
</style>