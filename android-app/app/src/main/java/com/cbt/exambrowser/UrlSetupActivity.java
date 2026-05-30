package com.cbt.exambrowser;

import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.util.Patterns;
import android.view.View;
import android.widget.Toast;
import androidx.appcompat.app.AppCompatActivity;
import com.google.android.material.button.MaterialButton;
import com.google.android.material.textfield.TextInputEditText;

public class UrlSetupActivity extends AppCompatActivity {

    private TextInputEditText inputUrl;
    private MaterialButton btnConnect;
    private SharedPreferences sharedPreferences;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_url_setup);

        inputUrl = findViewById(R.id.input_url);
        btnConnect = findViewById(R.id.btn_connect);
        sharedPreferences = getSharedPreferences("CBT_PREFS", Context.MODE_PRIVATE);

        // Pre-fill existing URL if available
        String currentUrl = sharedPreferences.getString("server_url", "");
        if (!currentUrl.isEmpty()) {
            inputUrl.setText(currentUrl);
        }

        btnConnect.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                saveUrl();
            }
        });
    }

    private void saveUrl() {
        String url = inputUrl.getText().toString().trim();

        if (url.isEmpty()) {
            Toast.makeText(this, "Alamat Server CBT tidak boleh kosong!", Toast.LENGTH_SHORT).show();
            return;
        }

        // Auto format: Add http:// if user forgets
        if (!url.startsWith("http://") && !url.startsWith("https://")) {
            url = "http://" + url;
        }

        // Validate URL format
        if (!Patterns.WEB_URL.matcher(url).matches()) {
            Toast.makeText(this, getString(R.string.invalid_url), Toast.LENGTH_SHORT).show();
            return;
        }

        // Save URL
        SharedPreferences.Editor editor = sharedPreferences.edit();
        editor.putString("server_url", url);
        editor.apply();

        Toast.makeText(this, "Server berhasil disimpan!", Toast.LENGTH_SHORT).show();

        // Redirect to MainActivity
        Intent intent = new Intent(UrlSetupActivity.this, MainActivity.class);
        intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP | Intent.FLAG_ACTIVITY_NEW_TASK);
        startActivity(intent);
        finish();
    }
}
