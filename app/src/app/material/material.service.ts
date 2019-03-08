import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from 'src/environments/environment';

@Injectable({providedIn: 'root'})

export class MaterialService {

    private API = environment.ApiUrl;

    constructor (private http: HttpClient) {}

    getDisciplines() {
        return this.http.get(this.API + '/material/getDisciplines', {observe: 'response'});
    }

    getMaterial(unitId, userId) {
        return this.http.post(this.API + '/material/getMaterials', {unitId, userId}, {observe: 'response'});
    }

    saveMaterial(data: FormData) {
        return this.http.post(this.API + '/material/saveMaterial', data, {observe: 'response'});
    }

    deleteMaterial(materialId: number) {
        return this.http.post(this.API + '/material/deleteMaterial', {materialId}, {observe: 'response'});
    }
}
