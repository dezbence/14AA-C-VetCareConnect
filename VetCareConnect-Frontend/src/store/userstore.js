import { defineStore } from "pinia";
import userservice from "../services/userservice";
import ownerservice from "@/services/ownerservice";

export const useUserStore = defineStore('userstore',{
    state : ()=>({
        user: {
            name: '',
            token: '',
            role:null           
        },
        status:{
            loggedIn:false,
            message:''
        },
        pets: [],
        show: false,
        showSure: false,
        cancelAppointmentId: null,
        petDelete: false,
        deletePetId: null
    }),
    getters:{},
    actions:{
        login(data){
            return userservice.login(data)
                .then(resp =>{
                    this.status.loggedIn = true;
                    this.user = resp.data.data;
                    this.status.message = ''; //resp.data.message;
                    localStorage.setItem('user',JSON.stringify(this.user))
                })
                .catch(err =>{
                    this.status.loggedIn = false;
                    this.user =  {name: '', token: '', role:null}
                    this.status.message = err.data.data.error;
                    return Promise.reject(err.resposne);
                })
        },
        logout(){
            return userservice.logout(this.user.token)
                .then(()=>{
                    this.status.loggedIn = false;
                    this.user =  {name: '', token: '', role:null}
                    localStorage.removeItem('user');
                });
        },
        showAppointmentCancel(value) {
            this.show = value;
        },
        showPetDelete(value){
            this.petDelete = value;
        },
        getPets() {
            ownerservice.getOwnersPets(this.user.token)
                .then((resp) => {
                    this.pets= resp.data;
                });
        }
    } 
});