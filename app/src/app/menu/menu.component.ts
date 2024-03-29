import { Component, OnDestroy, ChangeDetectorRef, OnInit } from '@angular/core';
import { MediaMatcher } from '@angular/cdk/layout';
import { Observable } from 'rxjs';
import { User } from '../user/user';
import { UserService } from '../user/user.service';
import { Router } from '@angular/router';
import { environment } from 'src/environments/environment';

@Component({
    selector: 'app-menu',
    templateUrl: './menu.component.html',
    styleUrls: ['./menu.component.scss'],
})
export class MenuComponent implements OnDestroy, OnInit {

    mobileQuery: MediaQueryList;

    user$: Observable<User>;
    API = environment.ApiUrl;


    private _mobileQueryListener: () => void;


    constructor(
        private changeDetectorRef: ChangeDetectorRef,
        private media: MediaMatcher,
        private userService: UserService,
        private router: Router) {

        this.mobileQuery = this.media.matchMedia('(max-width: 600px)');
        this._mobileQueryListener = () => this.changeDetectorRef.detectChanges();
        this.mobileQuery.addListener(this._mobileQueryListener);

        this.user$ = this.userService.getUser();
    }

    ngOnInit(): void {
    }

    ngOnDestroy(): void {
        this.mobileQuery.removeListener(this._mobileQueryListener);
    }

    logout() {
        this.userService.logout();
        this.router.navigate(['']);
    }

    getTypeProfile(profileId) {
        switch (profileId) {
            case '1':
                return 'Estudante';
            case '2':
                return 'Professor';
            case '3':
                return 'Monitor';
            case '4':
                return 'Coordenação';
            default:
                return '';
        }
    }

}
