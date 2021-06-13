import { Injectable } from '@angular/core';
import { Camera, CameraResultType, CameraSource} from '@capacitor/camera';
import { Photo } from '../interface/photo';





@Injectable({
  providedIn: 'root'
})
export class PhotoService {
  public photos: Photo[] = [];


  constructor() { }
  
  public async addNewToGallery() {
    // Take a photo
    const capturedPhoto = await Camera.getPhoto({
      
      resultType: CameraResultType.Uri,
      source: CameraSource.Camera,
      quality: 100
    });


    console.log('capture' , capturedPhoto.path);


    this.photos.unshift({
      property :capturedPhoto,
      filepath: "soon...",
      webviewPath: capturedPhoto.webPath,
      daturl : capturedPhoto.dataUrl
    });
    
  }
}
  



