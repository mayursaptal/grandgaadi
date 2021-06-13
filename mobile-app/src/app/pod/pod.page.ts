import { Component, OnInit } from '@angular/core';
import { PhotoService } from '../services/photo.service';
import { ActivatedRoute, Router } from '@angular/router';
import { ApiService } from '../Service/api.service';

@Component({
  selector: 'app-pod',
  templateUrl: './pod.page.html',
  styleUrls: ['./pod.page.scss'],
})
export class PodPage implements OnInit {
  public shipment_id: any;
  public formdata: any;
  constructor(
    private router: Router,
    public photoService: PhotoService,
    public api: ApiService,
    private route: ActivatedRoute
  ) {}
  ngOnInit() {
    this.route.queryParams.subscribe((params) => {
      this.shipment_id = params.id;
    });
  }

  addPhotoToGallery() {
    this.photoService.addNewToGallery();
  }

  async  onSubmit() {
    this.formdata = new FormData();

    this.formdata.append('shipment', this.shipment_id);

    let counter = 0;


    for (let index = 0; index <  this.photoService.photos.length; index++) {
      const element =  this.photoService.photos[index];
      counter++;
      let response = await fetch(element.webviewPath!);
      let blob = await response.blob();
      this.formdata.append('shipment_images[]', blob, counter + '.png');
     
    }


    const apikey = localStorage.getItem('apikey');
    this.api.post(apikey + '/shipment/update', this.formdata, true).subscribe(
      (data: any) => {
        this.api.loaderhide();
        this.router.navigateByUrl('/');
        this.api.toastMsg('Updated Successfully');
      this.photoService.photos=[]
      },

      (error) => {
        this.api.toastMsg('Something went wrong');
        this.api.loaderhide();
      }
    );
  }
}
