#include<stdio.h>
#define N 100
int main(){
	int i,j,t;
	int x,y,r,z;
	int n,m;
	int num=0;
	int a[N][N]={0};
	int b[N][N]={0};
	scanf("%d%d",&n,&m);
	for(i=1;i<=n;i++){
		for(j=1;j<=n;j++){
			a[i][j]=++num; 
		}
	}
	for(t=0;t<m;t++){
		scanf("%d%d%d%d",&x,&y,&r,&z);
		if(z==0){
			for(i=1;i<=r;i++){
			    for(j=1;j<=2*i+1;j++){
			    	b[x-i+j-1][y-i]=a[x+i][y-i+j-1];
			    	b[x+i][y-i+j-1]=a[x+i-j+1][y+i];
			    	b[x+i-j+1][y+i]=a[x-i][y+i-j+1];
			    	b[x-i][y+i-j+1]=a[x-i+j-1][y-i];
			    }
		    	for(j=1;j<=2*i+1;j++){
			    	a[x-i+j-1][y-i]=b[x-i+j-1][y-i];
		     		a[x+i][y-i+j-1]=b[x+i][y-i+j-1];
		    		a[x+i-j+1][y+i]=b[x+i-j+1][y+i];
			    	a[x-i][y+i-j+1]=b[x-i][y+i-j+1];
			    }
	    	}
		}else{
			for(i=1;i<=r;i++){
			    for(j=1;j<=2*i+1;j++){
			    	b[x-i+j-1][y-i]=a[x-i][y+i-j+1];
			    	b[x+i][y-i+j-1]=a[x-i+j-1][y-i];
			    	b[x+i-j+1][y+i]=a[x+i][y-i+j-1];
			    	b[x-i][y+i-j+1]=a[x+i-j+1][y+i];
			    }
		    	for(j=1;j<=2*i+1;j++){
			    	a[x-i+j-1][y-i]=b[x-i+j-1][y-i];
		     		a[x+i][y-i+j-1]=b[x+i][y-i+j-1];
		    		a[x+i-j+1][y+i]=b[x+i-j+1][y+i];
			    	a[x-i][y+i-j+1]=b[x-i][y+i-j+1];
			    }
	    	}
		}
	}
	for(i=1;i<=n;i++){
		for(j=1;j<=n;j++){
			printf("%d ",a[i][j]); 
		}
		printf("\n");
	}
	return 0;
} 
