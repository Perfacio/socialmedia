<template>
  <main>
    <transition name="bounce">
      <div class="modal" v-if="showModalReg">
        <span v-on:click="showModalReg=!showModalReg">X</span>
        <div class="form">
          <h2 v-if="successRegistration">Вы успешно зарегистрированы</h2>
          <form v-else>
            <label>
              <input
                placeholder="Имя"
                type="text"
                v-model="name"
                :class="{error: isError && !name}"
              />
              <span v-show="isError && !name">Котик, введи имя!</span>
            </label>

            <label>
              <input
                placeholder="Логин"
                type="text"
                v-model="login"
                :class="{error: isError && !login}"
              />
              <span v-show="isError && !login">Котик, введи логин!</span>
            </label>
            <label>
              <input
                placeholder="Пароль"
                type="password"
                v-model="password"
                :class="{error: isError && (!password || password !== checkPass)}"
              />
            </label>
            <label>
              <input
                placeholder="Повтори пароль"
                type="password"
                v-model="checkPass"
                :class="{error: isError && (!checkPass || password !== checkPass)}"
              />
              <span
                v-show="isError && (!checkPass || !password || password !== checkPass)"
              >Пароли не совпадают!</span>
            </label>
            <button @click="submit">Зарегистрироваться</button>
          </form>
        </div>
      </div>
    </transition>
    <transition name="bounce">
      <div class="modal" v-if="showModalEnter">
        <span v-on:click="showModalEnter=!showModalEnter">X</span>
        <div class="form">
          <form>
            <input type="text" placeholder="Введите логин" v-model="login" />
            <input type="password" placeholder="Введите пароль" v-model="password" />
            <button @click="authorize">Войти</button>
          </form>
        </div>
      </div>
    </transition>
    <div class="main">
      <video autoplay class="site-bg-video" loop muted poster>
        <source src="//localhost/src/media/site_bg.mp4" type="video/mp4" />
        <source src="//localhost/src/media/site_bg.webm" type="video/webm" />
      </video>
      <transition appear name="fade">
        <div class="body">
          <div class="form">
            <form>
              <img src="//localhost/src/media/logo-tr.png" alt="/pages/images/logo.png" />
              <button @click.prevent="showModalReg = !showModalReg">Зарегистрироваться</button>
              <button @click.prevent="showModalEnter = !showModalEnter">Войти</button>
            </form>
          </div>
        </div>
      </transition>
    </div>
  </main>
</template>

<script>
export default {
  name: "Main",
  data() {
    return {
      name: "",
      password: "",
      checkPass: "",
      showModalReg: false,
      showModalEnter: false,
      login: "",
      needCheck: false,
      successRegistration: false,
      news: [],
    };
  },
  computed: {
    isError() {
      if (this.needCheck) {
        return !(this.password === this.checkPass && this.name && this.login);
      }
      return false;
    },
  },
  methods: {
    authorize(event) {
      event.preventDefault();
      fetch("//localhost/api/user/authorize.php", {
        method: "post",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          login: this.login,
          password: this.password,
        }),
      })
        .then((res) => res.json())
        .then((res) => {
          // console.log(res);
          this.isFormOpen = false;
          this.isLogin = true;
          localStorage.setItem("id", res.userId);
          localStorage.setItem("login", this.login);
          if(res.userId){
          this.$router.push("Profile");}
          else{
            alert("Неправильный логин или пароль")
          }
          //    this.setStatus('online', res.userId);
        });
    },
    logout() {
      //    this.setStatus('offline', localStorage.getItem('id'));
      localStorage.setItem("id", "");
      localStorage.setItem("login", "");
      this.isLogin = false;
      this.login = "";
      this.password = "";
    },
    submit(event) {
      event.preventDefault();
      this.needCheck = true;
      if (!this.isError) {
        fetch("//localhost/api/user/registration.php", {
          method: "post",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({
            name: this.name,

            login: this.login,
            password: this.password,
          }),
        })
          .then((res) => res.json())
          .then((res) => {
            if (res && res.res) {
              this.name = "";

              this.login = "";

              this.password = "";
              this.checkPass = "";
              this.needCheck = false;
              this.successRegistration = true;
            }
          })
          .catch((err) => {
            console.log(err);
          });
      }
    },
  },
  mounted() {
    fetch("//localhost/api/news/index.php", {
      headers: {
        "Content-Type": "application/json",
      },
    })
      .then((res) => res.json())
      .then((res) => {
        // console.log(res);
        this.news = res;
      });
  },
};
</script>

<style lang="scss" scoped>
main {
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;

  //   .slide-fade-enter-active {
  //   transition: all .3s ease;
  // }
  // .slide-fade-leave-active {
  //   transition: all .8s cubic-bezier(1.0, 0.5, 0.8, 1.0);
  // }
  // .slide-fade-enter, .slide-fade-leave-to
  // /* .slide-fade-leave-active до версии 2.1.8 */ {
  //   transform: translateX(10px);
  //   opacity: 0;
  // }

  .bounce-enter-active {
    animation: bounce-in 0.9s;
  }
  .bounce-leave-active {
    animation: bounce-in 0.5s reverse;
  }
  @keyframes bounce-in {
    0% {
      transform: scale(0);
    }
    50% {
      transform: scale(1.2);
    }
    100% {
      transform: scale(1);
    }
  }

  .modal {
    position: fixed;
    width: 500px;
    height: 500px;
    background-color: rgba(0, 0, 0, 0.9);
    // margin: calc(50%+250px);
    margin-left: calc(50% - 250px);
    border-radius: 10px;
    border: 1px solid rgba(0, 0, 100, 0.3);
    // display: none;
    .form {
      margin-top: 40px;
      h2 {
        text-align: center;
        color: whitesmoke;
        margin-top: 5rem;
      }
    }
    span {
      &:hover {
        cursor: pointer;
      }
      right: 0;
      position: absolute;
      padding: 10px;
      color: whitesmoke;
      &:first-child:after {
        content: "";
        position: absolute;
        display: block;
        width: 20px;
        height: 20px;
        border: 2px solid whitesmoke;
        border-radius: 50%;
        top: 6px;
        left: 3px;
      }
    }

    form {
      display: flex;
      flex-direction: column;
      h2 {
        color: white;
      }
      button {
        width: 324px;
        /* min-width: 300px; */
        background: #2196f3;
        margin: 30px auto 10px;
        padding: 10px;
        border-color: #4a90e2;
      }
      label {
        margin: auto;
        span {
          font-size: 10px;
          font-weight: bold;
          color: #2196f3;
        }
      }
      input {
        width: 300px;
        min-width: 300px;
        background: #fff;
        margin: 30px auto 10px;
        padding: 10px;
        border-color: #4a90e2;
      }
    }
  }
  .main {
    display: flex;
    width: 100%;
    height: 100%;
    flex-direction: column;
    .fade-enter-active,
    .fade-leave-active {
      transition: opacity 1s;
    }
    .fade-enter, .fade-leave-to /* .fade-leave-active до версии 2.1.8 */ {
      opacity: 0;
    }
    .appear {
      transition: opacity 1s;
    }

    video {
      position: absolute;
      top: -5%;
      width: 100%;
      height: auto;
      z-index: -1;
    }

    .banner {
      height: 20%;
      width: 100%;
    }
    .body {
      height: 80%;
      width: 100%;
      display: flex;

      .news {
        width: 60%;
        height: 100%;
        margin-top: 2rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        .article {
          width: 80%;
          margin-bottom: 1rem;
          .head {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 0.5rem;
            div {
              &:first-child {
                font-size: 1.6rem;
              }
              &:last-child {
                color: red;
                font-size: 0.9rem;
              }
            }
          }
          .desc {
            width: 100%;
            box-sizing: border-box;
            padding-left: 2rem;
            // margin-bottom: .5rem;
            font-size: 1.1rem;
            font-style: italic;
          }
          .author {
            text-align: end;
            font-size: 0.9rem;
            font-weight: 600;
          }
        }
      }
      .form {
        width: 40%;
        height: 100%;
        padding: 16px 0 0 16px;
        box-sizing: border-box;
        margin: auto;
        h2 {
          margin: 16px 0 8px;
          font-size: 1.5rem;
          color: rgb(110, 170, 120);
        }
        form {
          display: flex;
          flex-direction: column;
          width: 100%;
          img {
            width: 200px;
            margin: auto;
          }

          label {
            display: flex;
            flex-direction: column;
            margin-bottom: 12px;
            span {
              text-transform: uppercase;
              margin-left: 4px;
              &:first-child {
                margin-bottom: 4px;
                color: rgba(110, 110, 130, 0.8);
                font-size: 1rem;
              }
              &:last-child {
                font-size: 0.6rem;
                margin-top: 4px;
                color: red;
              }
            }
            input {
              border: 2px solid rgba(110, 110, 130, 0.8);
              padding: 8px 4px;
              border-radius: 3px;
              font-size: 1rem;
              &.error {
                border-color: red;
              }
            }
          }
          button {
            width: 300px;
            min-width: 300px;
            background: #fff;
            margin: 30px auto 10px;
            padding: 10px;
            border-color: #4a90e2;

            &:hover {
              cursor: pointer;
            }
          }
        }
      }
    }
  }
}
</style>