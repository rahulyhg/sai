import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from 'src/environments/environment';

@Injectable({providedIn: 'root'})

export class StudentReportService {

    private API = environment.ApiUrl;

    constructor(private http: HttpClient) {}

    getStudents() {
        return this.http.get(this.API + '/report/students', {observe: 'response'});
    }
}
