package mot;
import java.util.Random;

import javafx.application.Application;
import javafx.stage.Stage;
import javafx.scene.Scene;
import javafx.scene.control.Button;
import javafx.scene.control.TextField;
import javafx.collections.FXCollections;
import javafx.collections.ObservableList;
import javafx.scene.layout.VBox;

public class MotLePlusLong extends Application{
	@Override
	public void start(Stage primaryStage) throws Exception{
		primaryStage.setTitle("Juste un bouton");
		VBox vbox =new VBox(10);
		TextField text=new TextField("tes");
		ObservableList<String> list=FXCollections.observableArrayList();
		for(int i=0;i<text.getText().length();i++) {
			char b=text.getText().charAt(i);
			list.add(i,String.valueOf(b));
			//add(text.getCharacters().charAt(i));
		}
		System.out.println(text.getText());
		Scene scene = new Scene(vbox,200,200);
		for(int i=0;i<text.getText().length();i++) {
			StringBuilder sb=new StringBuilder();
			Random r=new Random();
			sb.append(list.get(r.nextInt(text.getText().length())));
			vbox.getChildren().add(new Button(sb.toString()));
		}
		primaryStage.setScene(scene);
		primaryStage.show();
	}
	
	public static void main(String[] args) {
		launch(args);
	}
}
