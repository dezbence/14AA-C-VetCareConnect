// import { defineStore } from "pinia";

// export const usePetStore = defineStore('petStore', {
//     state: () => {
//         return {
//             id: Math.floor(Math.random() * 1000000),
//             name: "",
//             species: "",
//             gender: 2,
//             weight: 0.0,
//             born_date: "",
//             comment: ""
//         }
//     }
// })



localStorage.setItem("petStore", [{
    id: Math.floor(Math.random() * 1000000),
    name: "",
    species: "",
    gender: 2,
    weight: 0.0,
    born_date: "",
    comment: ""
}])
console.log(localStorage.getItem("petStore"))
