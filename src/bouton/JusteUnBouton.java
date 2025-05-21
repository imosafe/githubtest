package bouton;

import javafx.application.Application;
import javafx.event.ActionEvent;
import javafx.event.EventHandler;
import javafx.stage.Stage;
import javafx.scene.Scene;
import javafx.scene.control.Button;

public class JusteUnBouton extends Application {
	@Override
	public void start(Stage primaryStage) throws Exception {
		primaryStage.setTitle("Juste un bouton");
		Button b=new Button("");
		b.setId("b1");
		b.setOnAction(new EventHandler<ActionEvent>() {
			@Override
			public void handle(ActionEvent event) {
				b.setId("b2");
			}
		});
		
		//b.setPrefSize(400, 100);
		//b.setStyle("-fx-background-color:red;-fx-text-fill:white;-fx-border-color:blue;-fx-border-width:5px");
		Scene scene = new Scene(b,200,200);
		//b.setDisable(true);
		scene.getStylesheets().add(getClass().getResource("style.css").toExternalForm());
		//scene.getStylesheets().add("style.css");
		//b.setId(b.isDisable()? "b1":"b2");
		primaryStage.setScene(scene);
		primaryStage.show();
	}
	/*
	class MyBtnHandler implements EventHandler<ActionEvent>{
		@Override
		public void handle(ActionEvent event) {
			b.setId("b2");
		}
	}
	*/
	public static void main(String[] args) {
		launch(args);
	}
}



