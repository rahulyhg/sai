import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from 'src/environments/environment';

@Injectable({providedIn: 'root'})

export class ClassesService {

    private API = environment.ApiUrl;

    constructor(private http: HttpClient) {}

    getUnitClasses(unitId: number) {
        return this.http.post(this.API + '/classes/getUnitClasses', {unitId}, {observe: 'response'});
    }

    getClassStudents(classId: number) {
        return this.http.post(this.API + '/classes/getClassStudents', {classId}, {observe: 'response'});
    }
}
