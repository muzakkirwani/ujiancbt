package com.cbt.exambrowser;

import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.SharedPreferences;
import android.graphics.Bitmap;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.WindowManager;
import android.webkit.WebChromeClient;
import android.webkit.WebResourceError;
import android.webkit.WebResourceRequest;
import android.webkit.WebSettings;
import android.webkit.WebView;
import android.webkit.WebViewClient;
import android.widget.EditText;
import android.widget.Toast;
import androidx.appcompat.app.AlertDialog;
import androidx.appcompat.app.AppCompatActivity;
import com.google.android.material.floatingactionbutton.FloatingActionButton;
import com.google.android.material.progressindicator.LinearProgressIndicator;

public class MainActivity extends AppCompatActivity {

    private WebView webView;
    private LinearProgressIndicator progressLoader;
    private FloatingActionButton fabSettings;
    private SharedPreferences sharedPreferences;
    private String serverUrl;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        // Security: Block Screenshot & Screen Recording
        getWindow().setFlags(WindowManager.LayoutParams.FLAG_SECURE,
                WindowManager.LayoutParams.FLAG_SECURE);

        setContentView(R.layout.activity_main);

        sharedPreferences = getSharedPreferences("CBT_PREFS", Context.MODE_PRIVATE);
        serverUrl = sharedPreferences.getString("server_url", "");

        // If URL is not set, redirect to Setup screen
        if (serverUrl.isEmpty()) {
            Intent intent = new Intent(MainActivity.this, UrlSetupActivity.class);
            startActivity(intent);
            finish();
            return;
        }

        webView = findViewById(R.id.webview_cbt);
        progressLoader = findViewById(R.id.progress_loader);
        fabSettings = findViewById(R.id.fab_settings);

        setupWebView();

        webView.loadUrl(serverUrl);

        fabSettings.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                promptAdminPassword();
            }
        });
    }

    private void setupWebView() {
        WebSettings webSettings = webView.getSettings();
        webSettings.setJavaScriptEnabled(true);
        webSettings.setDomStorageEnabled(true);
        webSettings.setDatabaseEnabled(true);
        webSettings.setUseWideViewPort(true);
        webSettings.setLoadWithOverviewMode(true);
        webSettings.setSupportZoom(false);
        webSettings.setBuiltInZoomControls(false);
        webSettings.setDisplayZoomControls(false);
        webSettings.setAllowFileAccess(true);
        webSettings.setAllowContentAccess(true);

        // Prevent opening in external browser
        webView.setWebViewClient(new WebViewClient() {
            @Override
            public void onPageStarted(WebView view, String url, Bitmap favicon) {
                super.onPageStarted(view, url, favicon);
                progressLoader.setVisibility(View.VISIBLE);
            }

            @Override
            public void onPageFinished(WebView view, String url) {
                super.onPageFinished(view, url);
                progressLoader.setVisibility(View.GONE);
            }

            @Override
            public boolean shouldOverrideUrlLoading(WebView view, WebResourceRequest request) {
                return false; // Load inside WebView
            }

            @SuppressWarnings("deprecation")
            @Override
            public boolean shouldOverrideUrlLoading(WebView view, String url) {
                return false; // Load inside WebView
            }

            @Override
            public void onReceivedError(WebView view, WebResourceRequest request, WebResourceError error) {
                super.onReceivedError(view, request, error);
                progressLoader.setVisibility(View.GONE);
            }
        });

        webView.setWebChromeClient(new WebChromeClient() {
            @Override
            public void onProgressChanged(WebView view, int newProgress) {
                super.onProgressChanged(view, newProgress);
                progressLoader.setProgress(newProgress);
                if (newProgress == 100) {
                    progressLoader.setVisibility(View.GONE);
                } else {
                    progressLoader.setVisibility(View.VISIBLE);
                }
            }
        });
    }

    private void promptAdminPassword() {
        AlertDialog.Builder builder = new AlertDialog.Builder(this);
        builder.setTitle("Menu Pengawas");
        builder.setMessage("Masukkan PIN/Sandi Pengawas untuk mengubah alamat server:");

        final EditText input = new EditText(this);
        input.setHint("Sandi Pengawas");
        input.setInputType(android.text.InputType.TYPE_CLASS_TEXT | android.text.InputType.TYPE_TEXT_VARIATION_PASSWORD);
        builder.setView(input);

        builder.setPositiveButton("OK", new DialogInterface.OnClickListener() {
            @Override
            public void onClick(DialogInterface dialog, int which) {
                String pass = input.getText().toString();
                // Default invigilator password: admin123
                if ("admin123".equals(pass) || "pengawas".equals(pass)) {
                    Intent intent = new Intent(MainActivity.this, UrlSetupActivity.class);
                    startActivity(intent);
                } else {
                    Toast.makeText(MainActivity.this, "Sandi Salah!", Toast.LENGTH_SHORT).show();
                }
            }
        });
        builder.setNegativeButton("Batal", null);
        builder.show();
    }

    @Override
    public void onBackPressed() {
        if (webView.canGoBack()) {
            webView.goBack();
        } else {
            // Ask for confirmation to exit (so student doesn't close exam accidentally)
            new AlertDialog.Builder(this)
                    .setTitle(R.string.dialog_exit_title)
                    .setMessage(R.string.dialog_exit_msg)
                    .setPositiveButton(R.string.yes, new DialogInterface.OnClickListener() {
                        @Override
                        public void onClick(DialogInterface dialog, int which) {
                            MainActivity.super.onBackPressed();
                        }
                    })
                    .setNegativeButton(R.string.no, null)
                    .show();
        }
    }
}
