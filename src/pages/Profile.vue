<template>
  <div class="profile">
    <video autoplay class="site-bg-video" loop muted poster>
      <source src="//localhost/src/media/site_bg.mp4" type="video/mp4" />
      <source src="//localhost/src/media/site_bg.webm" type="video/webm" />
    </video>

    <div class="body">
      <transition appear name="fade">
        <div class="menu">
          <div class="card">
            <div class="head">
              <label>
                <img :src="avatar" :alt="name" />
                <span>Изменить</span>
                <input type="file" name="avatar" @change="upload" />
              </label>
              <div>{{name}}</div>
            </div>
            <div class="body">
              <div @click="changepass">Сменить Пароль</div>
              <div @click="logout">Выйти</div>
            </div>
          </div>
        </div>
      </transition>
      <transition name="fade">
        <div class="form">
          <form>
            <!-- <label>
            <span>Имя</span>
            <input type="text" v-model="name" :class="{error: isError && !name}" />
            <span v-show="isError && !name">Название не может быть пустым!</span>
          </label>
          <label>
            <span>Логин</span>
            <input type="text" v-model="login" :class="{error: isError && !login}" />
            <span v-show="isError && !login">Название не может быть пустым!</span>
            </label>-->
            <!-- <label>
                        <span>Тип</span>
                        <input type="text"
                               v-model="type"
                               :class="{error: isError && !type}"/>
                        <span v-show="isError && !type">
                            Тип не может быть пустым!
                        </span>
            </label>-->
            <!-- <label>
                        <span>Дата открытия</span>
                        <input type="date"
                               v-model="dateOpen"
                               :class="{error: isError && !dateOpen}"/>
                        <span v-show="isError && !dateOpen">
                            Дата открытия не может быть пустой!
                        </span>
            </label>-->
            <!-- <label>
                        <span>Логин</span>
                        <input type="text"
                               v-model="login"
                               :class="{error: isError && !login}"/>
                        <span v-show="isError && !login">
                            Логин не может быть пустым!
                        </span>
            </label>-->
            <label v-if="passchange">
              <span>Пароль</span>
              <input
                type="password"
                v-model="password"
                :class="{error: isError && (password !== checkPass)}"
              />
            </label>
            <label v-if="passchange">
              <span>Повторите пароль</span>
              <input
                type="password"
                v-model="checkPass"
                :class="{error: isError && (!checkPass || password !== checkPass)}"
              />
              <span v-show="isError && (!checkPass || password !== checkPass)">Пароли не совпадают!</span>
            </label>
            <button v-if="passchange" @click="submit">Сменить пароль!</button>
          </form>
        </div>
      </transition>
    </div>
  </div>
</template>

<script>
export default {
  name: "Profile",
  data() {
    return {
      name: "",
      type: "",
      password: "",
      checkPass: "",
      dateOpen: "",
      login: "",
      needCheck: false,
      successRegistration: false,
      passchange: false,
      avatar: "",
    };
  },
  computed: {
    isError() {
      if (this.needCheck) {
        return !(
          this.password === this.checkPass &&
          this.name &&
          this.type &&
          this.login &&
          this.dateOpen
        );
      }
      return false;
    },
  },
  methods: {
    submit(event) {
      event.preventDefault();
      this.needCheck = true;
      if (!this.isError) {
        const body = {
          //   name: this.name,
          //   type: this.type,
          //   dateOpen: new Date(this.dateOpen).valueOf() / 1000,
          //   login: this.login,
          password: this.password,
        };
        // if (this.password) {
        //   body.password = this.password;
        // }
        fetch(
          "//localhost/api/user/user.php?id=" + localStorage.getItem("id"),
          {
            method: "put",
            headers: {
              "Content-Type": "application/json",
            },
            body: JSON.stringify(body),
          }
        )
          .then((res) => res.json())
          .then((res) => {
            console.log(res);
          });
      }
    },
    logout() {
      //    this.setStatus('offline', localStorage.getItem('id'));
      localStorage.setItem("id", "");
      localStorage.setItem("login", "");
      this.isLogin = false;
      this.login = "";
      this.password = "";
      this.$router.push("/");
    },
    changepass() {
      if (!this.passchange) {
        this.passchange = true;
      } else this.passchange = false;
    },
    upload(event) {
      console.log(event);
      const data = new FormData();
      data.append("file", event.target.files[0]);
      data.append("userId", localStorage.getItem("id"));
      fetch("//localhost/api/user/user.php", {
        method: "post",
        // headers: {
        // 'Content-Type': 'multipart/form-data'
        // },
        body: data,
      })
        .then((res) => res.json())
        .then((res) => {
          // console.log(res);
          if (res.res) {
            this.avatar = res.src;
          }
        });
    },
  },
  mounted() {
    fetch("//localhost/api/user/user.php?id=" + localStorage.getItem("id"), {
      method: "get",
      headers: {
        "Content-Type": "application/json",
      },
    })
      .then((res) => res.json())
      .then((res) => {
        console.log(res);
        const date = new Date(+res.user.dateOpen * 1000);
        let strDate = date.getFullYear() + "-";
        const month = "" + date.getMonth();
        const day = "" + date.getDate();

        strDate += (month.length < 2 ? "0" + month : month) + "-";
        strDate += day.length < 2 ? "0" + day : day;

        this.name = res.user.name;
        this.type = res.user.type;
        this.dateOpen = strDate;
        this.login = res.user.login;
        this.avatar = res.user.avatar || "//localhost/src/assets/orig.gif";
      });
  },
};
</script>

<style lang="scss" scoped>
.profile {
  display: flex;
  width: 100%;
  height: 100%;
  flex-direction: column;

  video {
    position: absolute;
    top: -5%;
    width: 100%;
    height: auto;
    z-index: -1;
  }
  .body {
    height: 80%;
    width: 100%;
    display: flex;
    .fade-enter-active,
    .fade-leave-active {
      transition: opacity 2s;
    }
    .fade-enter, .fade-leave-to /* .fade-leave-active до версии 2.1.8 */ {
      opacity: 0;
    }
    .appear {
      transition: opacity 2s;
    }

    .menu {
      width: 30%;
      height: 100%;

      .card {
        display: flex;
        flex-direction: column;
        margin: 3rem auto 0;
        width: 80%;
        background-color: #fff;
        box-shadow: 0 0 12px rgba(0, 0, 0, 0.1);
        box-sizing: border-box;
        border-radius: 3px;
        background-color: rgba(0, 0, 20, 0.2);
        color: whitesmoke;
        border: 1px solid rgba(110, 110, 130, 0.8);
        .head {
          text-align: center;
          padding: 1rem;
          box-sizing: border-box;
          label {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            overflow: hidden;
            position: relative;
            display: flex;
            margin: 0 auto;
            img {
              width: 100%;
              height: 100%;
            }
            span {
              display: flex;
              opacity: 0;
              position: absolute;
              width: 100%;
              height: 100%;
              align-items: center;
              justify-content: center;
              background-color: rgba(0, 0, 0, 0.6);
              font-size: 1.1rem;
              transition: 0.5s;
              color: #fff;
              letter-spacing: -0.8px;
              font-weight: 600;
            }
            input {
              display: none;
            }
            &:hover {
              cursor: pointer;
              span {
                // background-color: whitesmoke;
                opacity: 0.9;
                transition: 0.5s;
                color: rgba(0, 0, 20, 0.2);
                background-color: rgba(110, 110, 110, 0.8);
                //   transition: 0.5s;
              }
            }
          }
          div {
            font-size: 1.3rem;
            font-weight: 600;
          }
        }
        .body {
          display: flex;
          flex-direction: column;
          border-top: 2px solid rgba(0, 0, 0, 0.1);
          > div {
            padding: 1rem;
            box-sizing: border-box;
            transition: 0.5s;
            &:not(:last-child) {
              border-bottom: 0.5px solid rgba(0, 0, 0, 0.1);
            }
            &:hover {
              background-color: rgba(110, 110, 110, 0.8);
              transition: 0.5s;
              cursor: pointer;
            }
          }
        }
      }
    }
    .form {
      width: 20%;
      height: 100%;
      padding: 50px 0 0 16px;
      box-sizing: border-box;
      h2 {
        margin: 16px 0 8px;
        font-size: 1.5rem;
        color: rgb(110, 170, 120);
      }
      form {
        display: flex;
        flex-direction: column;
        width: 80%;
        margin: 0 auto;
        label {
          display: flex;
          flex-direction: column;
          margin-bottom: 12px;
          span {
            text-transform: uppercase;
            margin-left: 4px;
            &:first-child {
              margin-bottom: 4px;
              color: whitesmoke;
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
          display: flex;
          margin: 16px auto 0;
          border: 2px solid rgba(110, 110, 130, 0.8);
          padding: 12px 8px;
          text-transform: uppercase;
          border-radius: 2px;
          font-size: 1.1rem;
          font-weight: 500;
          &:hover {
            cursor: pointer;
          }
        }
      }
    }
  }
}
</style>