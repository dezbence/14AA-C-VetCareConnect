<template>
    <div class="darkBack">
        <div class="petCreatingForm">
            <h3>Hozza létre kedvence adatlapját!</h3>
            <form submit.pervent="handleSubmit"></form>

            <label>Kedvence neve:</label>
            <InputText v-model="pet.name"></InputText>

            <div class="officalNumbers">
                <div class="chipNum">
                    <label>Chip szám:</label>
                    <InputMask v-model="pet.chip_number" mask="999999999999999"></InputMask>
                </div>
                
                <div class="pedigreeNum">
                    <label>Törzskönyv száma:</label>
                <InputMask mask="99999999"></InputMask>
                </div>
                
            </div>

            <label>Fajtajelleg:</label>
            <Dropdown v-model="pet.spieces" :options="spieces" showClear placeholder="Kérem válasszon!" class="petDropdown" />

            <label>Ivar:</label>
            <Dropdown v-model="pet.gender" :options="genders" showClear placeholder="Kérem válasszon!" class="petDropdown" />

            <label>Súlya (kg):</label>

            <InputMask id="basic" v-model="pet.weight" mask="99.99" placeholder="0" />
            <label>Születési dátuma:</label>
            <InputMask id="basic" v-model="pet.born_date" placeholder="éééé.hh.nn" mask="9999.99.99" />
            <label>Megjegyzés:</label>
            <Textarea v-model="pet.comment" rows="4" cols="40" autoResize />

            <button @click="handleSubmit()">Létrehozás</button>

        </div>
    </div>
</template>

<script setup>
import InputText from 'primevue/inputtext';
import Dropdown from 'primevue/dropdown';
import InputMask from 'primevue/inputmask';
import Textarea from 'primevue/textarea';
import { ref } from 'vue';

const props = defineProps(['addPetToList', 'petsList'])
const emits = defineEmits(['submit'])

const spieces = ['kutya', 'macska', 'hörcsög', 'cápa']
const genders = ['fiú', 'lány']

const pet = ref({
    id: Math.floor(Math.random() * 1000000),
    name: "",
    chip_number: 0,
    spieces: "",
    gender: 0,
    weight: 0,
    born_date: "",
    comment: ""
})


function handleSubmit() {
    emits('submit', pet)
}
</script>

<style scoped>
.petCreatingForm {
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    background-color: white;
    border-radius: 7px;
    padding: 30px;
    max-height: 700px;
}

input {
    padding: 5px 20px 5px 10px;
    color: #000;
    border-radius: 7px;
    background-color: #ededed;
    border: 1px solid #c5c5c5;
    outline: none;
    max-height: 50px;
}

button {
    width: 300px;
    border: none;
    border-radius: 7px;
    background-color: #50B692;
    color: white;
    padding: 5px 10px;
    margin-top: 30px;
    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
}

label {
    margin-top: 12px;
}
.officalNumbers {
    display: flex;
    gap: 20px;
    align-items: center;

}
.chipNum, .pedigreeNum {
    display: flex;
    flex-direction: column;
}
.pedigreeNum {
    align-items: end;
}

.chipNum label, .pedigreeNum label{
   font-size: 0.9rem;
}
.pedigreeNum input {
    width: 110px;
    padding: 5px;
}

.chipNum input {
    width: 150px;
    padding: 5px;
}

.p-dropdown,
.p-inputtext {
    color: #000;
    border-radius: 7px;
    background-color: #ededed;
    border: 1px solid #c5c5c5;
    outline: none;
    width: 300px;
}
</style>